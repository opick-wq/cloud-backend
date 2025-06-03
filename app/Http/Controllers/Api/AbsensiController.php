<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService; // Pastikan path benar
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // Untuk manajemen tanggal dan waktu


class AbsensiController extends Controller
{
    protected $firebase;
    protected $jwtSecret;
    protected $attendanceCollection = 'attendances'; // Nama koleksi di Firestore
    protected $usersCollection = 'users'; // Asumsi nama koleksi untuk data pengguna/siswa

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
        $this->jwtSecret = env('JWT_SECRET');
    }

    private function getAuthUser(Request $request)
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        $token = str_replace('Bearer ', '', $authHeader);
        try {
            return JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }

    // --- Metode untuk Mahasiswa ---

    /**
     * Mahasiswa melakukan absensi (Datang atau Sakit).
     */
    public function submitStudentAttendance(Request $request)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser || $authUser->role !== 'siswa') { // Pastikan role 'siswa' sesuai dengan JWT Anda
            return response()->json(['message' => 'Hanya siswa yang dapat melakukan absensi.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'action' => 'required|string|in:DATANG,SAKIT',
            'latitude' => 'nullable|required_if:action,DATANG|numeric',
            'longitude' => 'nullable|required_if:action,DATANG|numeric',
            'address' => 'nullable|string|max:255',
            'work_code' => 'nullable|string|max:100',
            'notes' => 'nullable|required_if:action,SAKIT|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $now = Carbon::now(env('APP_TIMEZONE', 'UTC'))->toIso8601String(); // Timestamp saat ini UTC
        
        $appTimezone = env('APP_TIMEZONE', 'UTC');
        $currentDateForLogic = Carbon::now($appTimezone); 

        $todayStringForStorage = $currentDateForLogic->format('Y-m-d');

        $startOfDayTimestampForQuery = $currentDateForLogic->copy()->startOfDay()->setTimezone('UTC')->toIso8601String();
        $startOfNextDayTimestampForQuery = $currentDateForLogic->copy()->addDay()->startOfDay()->setTimezone('UTC')->toIso8601String();

        $filters = [
            ['fieldPath' => 'studentId', 'op' => 'EQUAL', 'value' => ['stringValue' => $authUser->sub]],
            ['fieldPath' => 'date', 'op' => 'GREATER_THAN_OR_EQUAL', 'value' => ['timestampValue' => $startOfDayTimestampForQuery]],
            ['fieldPath' => 'date', 'op' => 'LESS_THAN', 'value' => ['timestampValue' => $startOfNextDayTimestampForQuery]],
        ];
        $existingAttendanceResponse = $this->firebase->queryCollection($this->attendanceCollection, $filters, [], 1);
        $existingAttendanceDoc = null;
        $existingAttendanceId = null;

        if (isset($existingAttendanceResponse['documents']) && !empty($existingAttendanceResponse['documents'])) {
            $existingAttendanceDoc = $existingAttendanceResponse['documents'][0];
            $existingAttendanceId = basename($existingAttendanceDoc['name']);
        }

        // Mengambil nama siswa. Sesuaikan path field 'name' jika berbeda di koleksi users Anda
        $studentName = $authUser->name ?? 'Nama Siswa Tidak Diketahui'; // Fallback jika tidak ada di JWT
        if (isset($authUser->sub) && ($authUser->name ?? null) === null) { // Coba fetch jika tidak ada di JWT
            try {
                $userDoc = $this->firebase->getDocument($this->usersCollection, $authUser->sub);
                if (isset($userDoc['fields']['name']['stringValue'])) {
                    $studentName = $userDoc['fields']['name']['stringValue'];
                } elseif (isset($userDoc['fields']['username']['stringValue'])) { // Fallback ke username jika nama tidak ada
                     $studentName = $userDoc['fields']['username']['stringValue'];
                }
            } catch (\Exception $e) {
                // Biarkan $studentName sebagai fallback
            }
        }


        if ($data['action'] === 'DATANG') {
            if ($existingAttendanceDoc) {
                $currentStatus = $existingAttendanceDoc['fields']['status']['stringValue'] ?? null;
                if ($currentStatus === 'HADIR') {
                    return response()->json(['message' => 'Anda sudah melakukan absensi datang hari ini.'], 409);
                }
                return response()->json(['message' => 'Status absensi Anda hari ini sudah tercatat ('.$currentStatus.'). Tidak dapat melakukan Datang lagi.'], 409);
            }

            $attendanceId = (string) Str::uuid();
            $attendanceData = [
                'studentId' => $authUser->sub,
                'studentName' => $studentName,
                'date' => $todayStringForStorage, 
                'status' => 'HADIR',
                'clockInTime' => $now, 
                'clockInLatitude' => (float) $data['latitude'],
                'clockInLongitude' => (float) $data['longitude'],
                'clockInAddress' => $data['address'] ?? null,
                'workCode' => $data['work_code'] ?? null,
                'recordedBy' => 'student',
                'createdAt' => $now,
                'updatedAt' => $now,
                'year_month' => $currentDateForLogic->format('Y-m'), 
            ];
            $this->firebase->createDocument($this->attendanceCollection, $attendanceId, $attendanceData);
            $attendanceData['id'] = $attendanceId; // Tambahkan ID ke data yang dikembalikan
            return response()->json(['message' => 'Absensi datang berhasil dicatat.', 'data' => $this->formatAttendanceDataFromPreparedData($attendanceData, true)], 201);

        } elseif ($data['action'] === 'SAKIT') {
            if ($existingAttendanceDoc) { 
                $currentStatus = $existingAttendanceDoc['fields']['status']['stringValue'] ?? null;
                if ($currentStatus === 'SAKIT') {
                    return response()->json(['message' => 'Anda sudah tercatat sakit hari ini.'], 409);
                }
                if ($currentStatus === 'HADIR') { 
                    $updateData = [
                        'status' => 'SAKIT',
                        'notes' => $data['notes'],
                        'clockOutTime' => $now, 
                        'updatedAt' => $now,
                        'recordedBy' => 'student_update_sick',
                    ];
                    $this->firebase->updateDocument($this->attendanceCollection, $existingAttendanceId, $updateData);
                    $updatedDocFromFirebase = $this->firebase->getDocument($this->attendanceCollection, $existingAttendanceId); 
                    return response()->json(['message' => 'Status absensi Anda telah diperbarui menjadi Sakit.', 'data' => $this->formatAttendanceData($updatedDocFromFirebase, true)], 200);
                }
                return response()->json(['message' => 'Status absensi Anda saat ini ('.$currentStatus.') tidak dapat diubah menjadi Sakit melalui aksi ini.'], 409);

            } else { 
                $attendanceId = (string) Str::uuid();
                $attendanceData = [
                    'studentId' => $authUser->sub,
                    'studentName' => $studentName,
                    'date' => $todayStringForStorage, 
                    'status' => 'SAKIT',
                    'notes' => $data['notes'],
                    'clockInTime' => null, 
                    'recordedBy' => 'student',
                    'createdAt' => $now,
                    'updatedAt' => $now,
                    'year_month' => $currentDateForLogic->format('Y-m'), 
                ];
                $this->firebase->createDocument($this->attendanceCollection, $attendanceId, $attendanceData);
                $attendanceData['id'] = $attendanceId; // Tambahkan ID
                return response()->json(['message' => 'Keterangan sakit berhasil dicatat.', 'data' => $this->formatAttendanceDataFromPreparedData($attendanceData, false)], 201);
            }
        }
        return response()->json(['message' => 'Aksi tidak valid.'], 400); // Seharusnya tidak sampai sini karena validasi 'in:DATANG,SAKIT'
    }

    /**
     * Mendapatkan info absensi mahasiswa untuk hari ini.
     */
    public function getMyTodaysAttendance(Request $request)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser || $authUser->role !== 'siswa') { // Pastikan role 'siswa' sesuai
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $appTimezone = env('APP_TIMEZONE', 'UTC');
        $startOfDayInAppTz = Carbon::now($appTimezone)->startOfDay();
        $startOfNextDayInAppTz = Carbon::now($appTimezone)->addDay()->startOfDay();

        $startOfDayTimestampForQuery = $startOfDayInAppTz->copy()->setTimezone('UTC')->toIso8601String();
        $startOfNextDayTimestampForQuery = $startOfNextDayInAppTz->copy()->setTimezone('UTC')->toIso8601String();

        $filters = [
            ['fieldPath' => 'studentId', 'op' => 'EQUAL', 'value' => ['stringValue' => $authUser->sub]],
            ['fieldPath' => 'date', 'op' => 'GREATER_THAN_OR_EQUAL', 'value' => ['timestampValue' => $startOfDayTimestampForQuery]],
            ['fieldPath' => 'date', 'op' => 'LESS_THAN', 'value' => ['timestampValue' => $startOfNextDayTimestampForQuery]],
        ];

        $response = $this->firebase->queryCollection($this->attendanceCollection, $filters, [], 1);

        if (isset($response['documents']) && !empty($response['documents'])) {
            return response()->json($this->formatAttendanceData($response['documents'][0], true));
        } elseif (isset($response['error'])) {
            return response()->json([
                'message' => 'Gagal mengambil data absensi',
                'error_detail' => $response['error']['details'] ?? $response['error']['message'] ?? 'Unknown Firebase error'
            ], 500);
        }

        return response()->json(['message' => 'Belum ada data absensi untuk hari ini.'], 404);
    }


    // --- Metode untuk Guru BK ---

    /**
     * Mendapatkan laporan absensi harian untuk semua siswa (untuk Guru BK).
     */
    public function getDailyReport(Request $request)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser || $authUser->role !== 'guru_bk') {
            return response()->json(['message' => 'Hanya Guru BK yang dapat mengakses laporan ini.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $requestedDateString = $validator->validated()['date'];
        $appTimezone = env('APP_TIMEZONE', 'UTC');

        try {
            $dateObjectInAppTz = Carbon::createFromFormat('Y-m-d', $requestedDateString, $appTimezone);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format tanggal tidak valid.'], 400);
        }

        $startOfDayInAppTz = $dateObjectInAppTz->copy()->startOfDay();
        $startOfNextDayInAppTz = $dateObjectInAppTz->copy()->addDay()->startOfDay();

        $startOfDayTimestampForQuery = $startOfDayInAppTz->copy()->setTimezone('UTC')->toIso8601String();
        $startOfNextDayTimestampForQuery = $startOfNextDayInAppTz->copy()->setTimezone('UTC')->toIso8601String();

        $filters = [
            ['fieldPath' => 'date', 'op' => 'GREATER_THAN_OR_EQUAL', 'value' => ['timestampValue' => $startOfDayTimestampForQuery]],
            ['fieldPath' => 'date', 'op' => 'LESS_THAN', 'value' => ['timestampValue' => $startOfNextDayTimestampForQuery]],
        ];

        $orderBy = [
            ['field' => 'date', 'direction' => 'ASCENDING'], // Meskipun tanggalnya sama, ini baik untuk konsistensi query
            ['field' => 'studentName', 'direction' => 'ASCENDING']
        ];

        $attendanceResponse = $this->firebase->queryCollection($this->attendanceCollection, $filters, $orderBy);

        $reportData = [];
        if (isset($attendanceResponse['documents']) && !empty($attendanceResponse['documents'])) {
            foreach ($attendanceResponse['documents'] as $doc) {
                $formattedData = $this->formatAttendanceData($doc, true); 
                if ($formattedData) {
                    $reportData[] = $formattedData;
                }
            }
        } elseif (isset($attendanceResponse['error'])) {
            return response()->json([
                'message' => 'Gagal mengambil laporan absensi',
                'error_detail' => $attendanceResponse['error']['details'] ?? $attendanceResponse['error']['message'] ?? 'Unknown Firebase error'
            ], 500);
        }

        return response()->json($reportData); 
    }

    public function getMonthlyReport(Request $request)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser || $authUser->role !== 'guru_bk') {
            return response()->json(['message' => 'Hanya Guru BK yang dapat mengakses laporan ini.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 5), 
            'month' => 'required|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $validatedData = $validator->validated();
        $year = (int)$validatedData['year'];
        $month = (int)$validatedData['month'];
        $appTimezone = env('APP_TIMEZONE', 'UTC');

        try {
            $startOfMonthInAppTz = Carbon::createFromDate($year, $month, 1, $appTimezone)->startOfMonth();
            $startOfNextMonthInAppTz = $startOfMonthInAppTz->copy()->addMonth()->startOfMonth();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Tahun atau bulan tidak valid.'], 400);
        }

        $startOfMonthTimestampForQuery = $startOfMonthInAppTz->copy()->setTimezone('UTC')->toIso8601String();
        $startOfNextMonthTimestampForQuery = $startOfNextMonthInAppTz->copy()->setTimezone('UTC')->toIso8601String();

        $filters = [
            ['fieldPath' => 'date', 'op' => 'GREATER_THAN_OR_EQUAL', 'value' => ['timestampValue' => $startOfMonthTimestampForQuery]],
            ['fieldPath' => 'date', 'op' => 'LESS_THAN', 'value' => ['timestampValue' => $startOfNextMonthTimestampForQuery]],
        ];
        $orderBy = [
            ['field' => 'date', 'direction' => 'ASCENDING'],
            ['field' => 'studentName', 'direction' => 'ASCENDING']
        ];

        $allMonthlyAttendanceResponse = $this->firebase->queryCollection($this->attendanceCollection, $filters, $orderBy);
        $studentAttendanceMap = [];

        if (isset($allMonthlyAttendanceResponse['documents']) && !empty($allMonthlyAttendanceResponse['documents'])) {
            foreach ($allMonthlyAttendanceResponse['documents'] as $doc) {
                $attendance = $this->formatAttendanceData($doc); 
                if (!$attendance || !isset($attendance['studentId']) || !isset($attendance['date'])) {
                    continue;
                }

                if (!isset($studentAttendanceMap[$attendance['studentId']])) {
                    $studentAttendanceMap[$attendance['studentId']] = [
                        'studentId' => $attendance['studentId'],
                        'studentName' => $attendance['studentName'] ?? 'Nama Tidak Ada', 
                        'days' => [] 
                    ];
                }
                try {
                    $localDate = Carbon::parse($attendance['date'])->timezone($appTimezone);
                    $dayOfMonth = $localDate->day;
                    $studentAttendanceMap[$attendance['studentId']]['days'][$dayOfMonth] = $attendance['status'];
                } catch (\Exception $e) {
                    // Gagal parse tanggal, log atau lewati
                }
            }
        } elseif (isset($allMonthlyAttendanceResponse['error'])) {
            return response()->json(['message' => 'Gagal mengambil laporan bulanan', 'error_detail' => $allMonthlyAttendanceResponse['error']['details'] ?? $allMonthlyAttendanceResponse['error']['message'] ?? 'Unknown Firebase error'], 500);
        }

        $report = array_values($studentAttendanceMap); 

        return response()->json([
            'report' => $report,
            'month' => $month,
            'year' => $year,
            'daysInMonth' => $startOfMonthInAppTz->daysInMonth 
        ]);
    }

    // --- METODE BARU UNTUK EDIT DAN MANUAL ADD OLEH GURU BK ---

    /**
     * Guru BK melakukan update data absensi siswa.
     */
    public function updateAttendance(Request $request, $attendanceId)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser || $authUser->role !== 'guru_bk') {
            return response()->json(['message' => 'Hanya Guru BK yang dapat melakukan aksi ini.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'work_code' => 'nullable|string|max:100',
            'clock_in_time' => 'nullable|date_format:Y-m-d\TH:i:s.\0\0\0\Z', // ISO8601 UTC
            'clock_out_time' => 'nullable|date_format:Y-m-d\TH:i:s.\0\0\0\Z', // ISO8601 UTC
            'status' => 'required|string|in:HADIR,SAKIT,IZIN,ALPHA', // Sesuaikan status jika perlu
            'notes' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            // 'date' field tidak diupdate, karena ini update untuk record di tanggal tersebut
            // 'student_id' juga tidak diupdate, karena ini update untuk record siswa yang sudah ada
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $now = Carbon::now(env('APP_TIMEZONE', 'UTC'))->toIso8601String();

        // Ambil dokumen yang ada untuk memastikan ID valid dan untuk referensi studentId/studentName
        try {
            $existingDoc = $this->firebase->getDocument($this->attendanceCollection, $attendanceId);
            if (!$existingDoc || !isset($existingDoc['fields'])) {
                return response()->json(['message' => 'Data absensi tidak ditemukan.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil data absensi: ' . $e->getMessage()], 500);
        }


        $updateData = [
            'status' => $data['status'],
            'updatedAt' => $now,
            'recordedBy' => $authUser->role . '_update', // Penanda siapa yang update
        ];

        // Hanya update field jika ada nilainya di request atau jika boleh di-null-kan
        if (array_key_exists('work_code', $data)) $updateData['workCode'] = $data['work_code']; // Perhatikan case field 'workCode'
        if (array_key_exists('notes', $data)) $updateData['notes'] = $data['notes'];
        
        // Untuk waktu, jika dikirim string kosong atau null, set ke null di DB
        $updateData['clockInTime'] = !empty($data['clock_in_time']) ? $data['clock_in_time'] : null;
        $updateData['clockOutTime'] = !empty($data['clock_out_time']) ? $data['clock_out_time'] : null;

        // Untuk lokasi, update jika ada nilainya
        if (array_key_exists('latitude', $data) && $data['latitude'] !== null) $updateData['clockInLatitude'] = (float) $data['latitude'];
        if (array_key_exists('longitude', $data) && $data['longitude'] !== null) $updateData['clockInLongitude'] = (float) $data['longitude'];
        if (array_key_exists('address', $data)) $updateData['clockInAddress'] = $data['address'];

        try {
            $this->firebase->updateDocument($this->attendanceCollection, $attendanceId, $updateData);
            $updatedDocFromFirebase = $this->firebase->getDocument($this->attendanceCollection, $attendanceId);
            return response()->json(['message' => 'Absensi berhasil diperbarui.', 'data' => $this->formatAttendanceData($updatedDocFromFirebase, true)], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui absensi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Guru BK menambahkan data absensi manual untuk siswa.
     */
    public function manualAddAttendance(Request $request)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser || $authUser->role !== 'guru_bk') {
            return response()->json(['message' => 'Hanya Guru BK yang dapat melakukan aksi ini.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string', // ID siswa yang akan diabsenkan
            'student_name' => 'nullable|string|max:255', // Nama siswa (opsional, bisa diambil dari DB jika student_id ada)
            'date' => 'required|date_format:Y-m-d', // Tanggal absensi
            'work_code' => 'nullable|string|max:100',
            'clock_in_time' => 'nullable|date_format:H:i', // Format Jam:Menit dari frontend, akan digabung dengan 'date'
            'clock_out_time' => 'nullable|date_format:H:i', // Format Jam:Menit dari frontend
            'status' => 'required|string|in:HADIR,SAKIT,IZIN,ALPHA',
            'notes' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $now = Carbon::now(env('APP_TIMEZONE', 'UTC'))->toIso8601String();
        $appTimezone = env('APP_TIMEZONE', 'UTC');

        // Gabungkan tanggal dari input 'date' dengan waktu dari 'clock_in_time' / 'clock_out_time'
        $attendanceDateCarbon = Carbon::createFromFormat('Y-m-d', $data['date'], $appTimezone)->startOfDay();

        $clockInTimestamp = null;
        if (!empty($data['clock_in_time'])) {
            list($hours, $minutes) = explode(':', $data['clock_in_time']);
            $clockInTimestamp = $attendanceDateCarbon->copy()->setTime($hours, $minutes)->setTimezone('UTC')->toIso8601String();
        }

        $clockOutTimestamp = null;
        if (!empty($data['clock_out_time'])) {
            list($hours, $minutes) = explode(':', $data['clock_out_time']);
            $clockOutTimestamp = $attendanceDateCarbon->copy()->setTime($hours, $minutes)->setTimezone('UTC')->toIso8601String();
        }

        // Ambil nama siswa jika tidak disediakan, atau gunakan yang disediakan
        $studentName = $data['student_name'] ?? 'Siswa'; // Fallback
        if (empty($data['student_name']) && !empty($data['student_id'])) {
            try {
                $userDoc = $this->firebase->getDocument($this->usersCollection, $data['student_id']);
                 if (isset($userDoc['fields']['name']['stringValue'])) {
                    $studentName = $userDoc['fields']['name']['stringValue'];
                } elseif (isset($userDoc['fields']['username']['stringValue'])) {
                     $studentName = $userDoc['fields']['username']['stringValue'];
                }
            } catch (\Exception $e) {
                // Gagal fetch nama, gunakan fallback atau ID siswa
                $studentName = $data['student_id']; 
            }
        }
        
        // Cek duplikasi untuk student_id dan date (format Y-m-d di Firebase)
        $startOfDayForManualDate = $attendanceDateCarbon->copy()->setTimezone('UTC')->toIso8601String();
        $startOfNextDayForManualDate = $attendanceDateCarbon->copy()->addDay()->setTimezone('UTC')->toIso8601String();

        $filters = [
            ['fieldPath' => 'studentId', 'op' => 'EQUAL', 'value' => ['stringValue' => $data['student_id']]],
            ['fieldPath' => 'date', 'op' => 'GREATER_THAN_OR_EQUAL', 'value' => ['timestampValue' => $startOfDayForManualDate]],
            ['fieldPath' => 'date', 'op' => 'LESS_THAN', 'value' => ['timestampValue' => $startOfNextDayForManualDate]],
        ];
        $existingAttendanceResponse = $this->firebase->queryCollection($this->attendanceCollection, $filters, [], 1);
        if (isset($existingAttendanceResponse['documents']) && !empty($existingAttendanceResponse['documents'])) {
            return response()->json(['message' => 'Sudah ada data absensi untuk siswa ini pada tanggal tersebut. Gunakan fitur edit jika ingin mengubah.'], 409);
        }


        $newAttendanceId = (string) Str::uuid();
        $attendanceData = [
            'studentId' => $data['student_id'],
            'studentName' => $studentName,
            'date' => $data['date'], // Simpan sebagai YYYY-MM-DD, FirebaseService akan konversi
            'status' => $data['status'],
            'clockInTime' => $clockInTimestamp,
            'clockOutTime' => $clockOutTimestamp,
            'clockInLatitude' => isset($data['latitude']) ? (float)$data['latitude'] : null,
            'clockInLongitude' => isset($data['longitude']) ? (float)$data['longitude'] : null,
            'clockInAddress' => $data['address'] ?? null,
            'workCode' => $data['work_code'] ?? null,
            'notes' => $data['notes'] ?? null,
            'recordedBy' => $authUser->role . '_manual_add',
            'createdAt' => $now,
            'updatedAt' => $now,
            'year_month' => Carbon::createFromFormat('Y-m-d', $data['date'])->format('Y-m'),
        ];

        try {
            $this->firebase->createDocument($this->attendanceCollection, $newAttendanceId, $attendanceData);
            $attendanceData['id'] = $newAttendanceId; // Tambahkan ID ke data yang dikembalikan
            // Format data sebelum dikirim kembali
            return response()->json(['message' => 'Absensi manual berhasil ditambahkan.', 'data' => $this->formatAttendanceDataFromPreparedData($attendanceData, true)], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan absensi manual: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Helper untuk memformat data absensi dari Firestore.
     */
    private function formatAttendanceData($firestoreDoc, $includeLocation = false)
    {
        if (!$firestoreDoc || !isset($firestoreDoc['fields'])) {
            return null;
        }
        $fields = $firestoreDoc['fields'];
        // Helper untuk mengambil nilai timestamp atau null
        $getTimestampValue = function ($field) {
            return $field['timestampValue'] ?? null;
        };
        // Helper untuk mengambil nilai string atau null
        $getStringValue = function ($field) {
            return $field['stringValue'] ?? null;
        };
         // Helper untuk mengambil nilai numerik (double/integer) atau null
        $getNumericValue = function ($field) {
            return $field['doubleValue'] ?? ($field['integerValue'] ?? null);
        };

        $data = [
            'id' => basename($firestoreDoc['name']),
            'studentId' => $getStringValue($fields['studentId'] ?? null),
            'studentName' => $getStringValue($fields['studentName'] ?? 'N/A'),
            // 'date' dari Firestore adalah Timestamp, dikembalikan apa adanya (ISO string)
            'date' => $getTimestampValue($fields['date'] ?? null), 
            'status' => $getStringValue($fields['status'] ?? 'N/A'),
            'clockInTime' => $getTimestampValue($fields['clockInTime'] ?? null),
            'clockOutTime' => $getTimestampValue($fields['clockOutTime'] ?? null),
            'workCode' => $getStringValue($fields['workCode'] ?? null),
            'notes' => $getStringValue($fields['notes'] ?? null),
            'recordedBy' => $getStringValue($fields['recordedBy'] ?? null),
            'createdAt' => $getTimestampValue($fields['createdAt'] ?? null),
            'updatedAt' => $getTimestampValue($fields['updatedAt'] ?? null),
            'year_month' => $getStringValue($fields['year_month'] ?? null), // tambahkan jika ada
        ];

        if ($includeLocation) {
            $data['clockInLatitude'] = $getNumericValue($fields['clockInLatitude'] ?? null);
            $data['clockInLongitude'] = $getNumericValue($fields['clockInLongitude'] ?? null);
            $data['clockInAddress'] = $getStringValue($fields['clockInAddress'] ?? null);
        }
        return $data;
    }

    /**
     * Helper untuk memformat data absensi dari array yang sudah disiapkan (bukan dari Firestore doc langsung).
     * Berguna setelah createDocument agar formatnya konsisten.
     */
    private function formatAttendanceDataFromPreparedData(array $preparedData, $includeLocation = false)
    {
        // Data 'date' disimpan sebagai YYYY-MM-DD, tapi saat dibaca dari Firestore akan jadi Timestamp.
        // Agar konsisten, kita bisa ubah YYYY-MM-DD ke ISO string Timestamp UTC awal hari jika perlu.
        // Namun, untuk output API, biarkan FirebaseService/Firestore yang menentukan format akhir 'date' saat dibaca.
        // Saat ini, kita biarkan apa adanya dari $preparedData karena frontend yang akan menangani.
        // Jika `FirebaseService->createDocument` mengembalikan data yang sudah terformat Firestore, gunakan itu.
        // Asumsi $preparedData['date'] adalah YYYY-MM-DD string.
        // $preparedData['date'] = Carbon::createFromFormat('Y-m-d', $preparedData['date'])->startOfDay()->setTimezone('UTC')->toIso8601String();

        $data = [
            'id' => $preparedData['id'] ?? null,
            'studentId' => $preparedData['studentId'] ?? null,
            'studentName' => $preparedData['studentName'] ?? 'N/A',
            'date' => $preparedData['date'] ?? null, // Ini akan jadi YYYY-MM-DD jika belum diubah jadi timestamp
            'status' => $preparedData['status'] ?? 'N/A',
            'clockInTime' => $preparedData['clockInTime'] ?? null, // Sudah ISO8601 UTC atau null
            'clockOutTime' => $preparedData['clockOutTime'] ?? null, // Sudah ISO8601 UTC atau null
            'workCode' => $preparedData['workCode'] ?? null,
            'notes' => $preparedData['notes'] ?? null,
            'recordedBy' => $preparedData['recordedBy'] ?? null,
            'createdAt' => $preparedData['createdAt'] ?? null, // Sudah ISO8601 UTC
            'updatedAt' => $preparedData['updatedAt'] ?? null, // Sudah ISO8601 UTC
            'year_month' => $preparedData['year_month'] ?? null,
        ];

        if ($includeLocation) {
            $data['clockInLatitude'] = $preparedData['clockInLatitude'] ?? null;
            $data['clockInLongitude'] = $preparedData['clockInLongitude'] ?? null;
            $data['clockInAddress'] = $preparedData['clockInAddress'] ?? null;
        }
        return $data;
    }
}