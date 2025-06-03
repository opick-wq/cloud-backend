<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService; // Pastikan ini adalah path yang benar
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator; // Untuk validasi yang lebih kompleks jika perlu

class KasusSiswaController extends Controller
{
    protected $firebase;
    protected $jwtSecret;

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
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            // Pastikan payload token memiliki informasi yang dibutuhkan seperti 'sub', 'role', dan 'username' atau 'name'
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Store a newly created student case in storage.
     * Hanya bisa dilakukan oleh guru BK.
     */
    public function store(Request $request)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($authUser->role !== 'guru_bk') {
            return response()->json(['message' => 'Only counselors (Guru BK) can create student cases.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'student_user_id' => 'required|string', // ID unik siswa (sub dari JWT siswa)
            'topic' => 'required|string|max:255',
            'follow_up' => 'required|string|max:1000',
            'case_date' => 'required|date_format:Y-m-d', // Format tanggal YYYY-MM-DD
            'notes' => 'nullable|string|max:1000',
            // 'student_name' => 'nullable|string|max:255', // Opsional jika ingin mengirim nama siswa langsung
            // 'student_attendance_number' => 'nullable|string|max:50' // Opsional No. Absen
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $caseId = (string) Str::uuid();

        // Ambil nama siswa berdasarkan student_user_id dari Firebase
        // Anda perlu metode di FirebaseService seperti findUserById($userId)
        $studentUser = $this->firebase->get($data['student_user_id']); // Asumsi metode ini mengambil data user (siswa)
        $studentName = $studentUser['fields']['name']['stringValue'] ?? ($studentUser['fields']['username']['stringValue'] ?? 'Siswa tidak ditemukan');
        
        // Ambil nama Guru BK dari authUser (asumsi ada field 'name' di token, jika tidak gunakan 'username')
        // Atau, jika Anda menyimpan data lengkap user guru_bk di Firebase, bisa fetch juga.
        // Untuk contoh ini, kita asumsikan 'username' ada di token sebagai fallback jika 'name' tidak ada.
        $guruBkName = $authUser->name ?? ($this->firebase->get($authUser->sub)['fields']['name']['stringValue'] ?? $authUser->username);


        $caseData = [
            'id' => $caseId,
            'studentId' => $data['student_user_id'],
            'studentName' => $studentName, // Nama siswa yang dituju
            // 'studentAttendanceNumber' => $data['student_attendance_number'] ?? null,
            'guruBkId' => $authUser->sub, // ID Guru BK yang membuat
            'guruBkName' => $guruBkName, // Nama Guru BK yang membuat
            'topic' => $data['topic'],
            'followUp' => $data['follow_up'],
            'caseDate' => $data['case_date'],
            'notes' => $data['notes'] ?? null,
            'createdAt' => now()->toIso8601String(),
            'updatedAt' => now()->toIso8601String(),
        ];

        // Anda perlu metode createStudentCase di FirebaseService
        // $this->firebase->createStudentCase($caseId, $caseData);
        $this->firebase->createDocumentWithSpecificId('student_cases', $caseData, $caseId);


        return response()->json(['message' => 'Student case created successfully', 'data' => $caseData], 201);
    }

    /**
     * Display a listing of the student cases.
     * Guru BK melihat semua, Siswa melihat miliknya sendiri.
     */
    public function index(Request $request)
    {
        $authUser = $this->getAuthUser($request); // Ini akan bekerja karena JS mengirim header
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $casesData = [];
        $response = null;

        if ($authUser->role === 'guru_bk') {
            $response = $this->firebase->getAllJurnal('student_cases'); // Sesuaikan nama metode
        } elseif ($authUser->role === 'siswa') {
        $filters = [
            ['fieldPath' => 'studentId', 'op' => 'EQUAL', 'value' => ['stringValue' => $authUser->sub]]
        ];
    // Anda juga bisa menambahkan order by jika perlu:
    // $orderBy = [['field' => 'caseDate', 'direction' => 'DESCENDING']];
    // $response = $this->firebase->queryCollection('student_cases', $filters, $orderBy);
        $response = $this->firebase->queryCollection('student_cases', $filters);
            } else {
                return response()->json(['message' => 'You do not have permission to view student cases.'], 403);
            }

        if (isset($response['documents'])) {
            foreach ($response['documents'] as $doc) {
                $fields = $doc['fields'];
                $casesData[] = [
                    'id' => basename($doc['name']),
                    'studentId' => $fields['studentId']['stringValue'] ?? null,
                    'studentName' => $fields['studentName']['stringValue'] ?? 'N/A',
                    'guruBkId' => $fields['guruBkId']['stringValue'] ?? null,
                    'guruBkName' => $fields['guruBkName']['stringValue'] ?? 'N/A',
                    'topic' => $fields['topic']['stringValue'] ?? '',
                    'followUp' => $fields['followUp']['stringValue'] ?? '',
                    // KOREKSI DI SINI: Gunakan timestampValue untuk caseDate
                    'caseDate' => $fields['caseDate']['timestampValue'] ?? ($fields['caseDate']['stringValue'] ?? null),
                    'notes' => $fields['notes']['stringValue'] ?? null,
                    'createdAt' => $fields['createdAt']['timestampValue'] ?? ($fields['createdAt']['stringValue'] ?? null),
                    'updatedAt' => $fields['updatedAt']['timestampValue'] ?? ($fields['updatedAt']['stringValue'] ?? null),
                ];
            }

            // Sorting berdasarkan caseDate
            if (!empty($casesData)) {
                usort($casesData, function ($a, $b) {
                    // Pastikan caseDate ada dan valid sebelum di-parse strtotime
                    // strtotime akan mengembalikan false jika string tidak valid, yang akan dikonversi ke 0
                    $timeA = isset($a['caseDate']) && $a['caseDate'] ? strtotime($a['caseDate']) : 0;
                    $timeB = isset($b['caseDate']) && $b['caseDate'] ? strtotime($b['caseDate']) : 0;

                    if ($timeA == $timeB) {
                        // Jika tanggal kasus sama, urutkan berdasarkan createdAt
                        $createdAtA = isset($a['createdAt']) && $a['createdAt'] ? strtotime($a['createdAt']) : 0;
                        $createdAtB = isset($b['createdAt']) && $b['createdAt'] ? strtotime($b['createdAt']) : 0;
                        return $createdAtB - $createdAtA; // Terbaru dulu
                    }
                    return $timeB - $timeA; // Urutkan caseDate terbaru dulu
                });
            }
        } elseif (isset($response['error'])) {
            // Log error jika perlu: \Log::error('Firebase Error: ' . json_encode($response['error']));
            return response()->json(['message' => 'Failed to fetch student cases', 'error_detail' => $response['error']['message'] ?? 'Unknown error'], 500);
        }

        return response()->json($casesData);
    }

    /**
     * Display the specified student case.
     * Guru BK bisa lihat semua, Siswa hanya miliknya.
     */
    public function show(Request $request, $id)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Anda perlu metode getStudentCaseById di FirebaseService
        // $caseDoc = $this->firebase->getStudentCaseById($caseId);
        $caseDoc = $this->firebase->getJurnal($id);


        if (!$caseDoc || isset($caseDoc['error']) || !isset($caseDoc['fields'])) {
             $errorMessage = 'Student case not found or failed to fetch.';
            if (isset($caseDoc['error']['message']) && strpos(strtolower($caseDoc['error']['message']), 'not found') !== false) {
                 $errorMessage = 'Student case not found.';
            } else if (isset($caseDoc['error']['code']) && $caseDoc['error']['code'] === 5) { // Firestore error code 5 is NOT_FOUND
                 $errorMessage = 'Student case not found.';
            }
            return response()->json(['message' => $errorMessage], ($errorMessage === 'Student case not found.') ? 404 : 500);
        }

        $fields = $caseDoc['fields'];
        $caseData = [
            'id' => basename($caseDoc['name']),
            'studentId' => $fields['studentId']['stringValue'] ?? null,
            'studentName' => $fields['studentName']['stringValue'] ?? 'N/A',
            // 'studentAttendanceNumber' => $fields['studentAttendanceNumber']['stringValue'] ?? null,
            'guruBkId' => $fields['guruBkId']['stringValue'] ?? null,
            'guruBkName' => $fields['guruBkName']['stringValue'] ?? 'N/A',
            'topic' => $fields['topic']['stringValue'] ?? '',
            'followUp' => $fields['followUp']['stringValue'] ?? '',
            'caseDate' => $fields['caseDate']['timestampValue'] ?? ($fields['caseDate']['stringValue'] ?? null),
            'notes' => $fields['notes']['stringValue'] ?? null,
            'createdAt' => $fields['createdAt']['timestampValue'] ?? ($fields['createdAt']['stringValue'] ?? null),
            'updatedAt' => $fields['updatedAt']['timestampValue'] ?? ($fields['updatedAt']['stringValue'] ?? null),
        ];

        if ($authUser->role === 'siswa' && $caseData['studentId'] !== $authUser->sub) {
            return response()->json(['message' => 'You do not have permission to view this student case.'], 403);
        } elseif ($authUser->role !== 'guru_bk' && $authUser->role !== 'siswa') {
             return response()->json(['message' => 'You do not have permission to view student cases.'], 403);
        }

        return response()->json($caseData);
    }

    /**
     * Update the specified student case in storage.
     * Hanya bisa dilakukan oleh guru BK.
     */
    public function update(Request $request, $id)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($authUser->role !== 'guru_bk') {
            return response()->json(['message' => 'Only counselors (Guru BK) can update student cases.'], 403);
        }

        // Anda perlu metode getStudentCaseById di FirebaseService untuk memastikan kasus ada
        // $existingCase = $this->firebase->getStudentCaseById($caseId);
        $existingCase = $this->firebase->getJurnal($id);

        if (!$existingCase || isset($existingCase['error']) || !isset($existingCase['fields'])) {
            return response()->json(['message' => 'Student case not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            // student_user_id tidak diupdate, karena kasus tetap milik siswa yang sama
            'topic' => 'sometimes|required|string|max:255',
            'followUp' => 'sometimes|required|string|max:1000',
            'caseDate' => 'sometimes|required|date_format:Y-m-d',
            'notes' => 'nullable|string|max:1000',
            // 'student_name' => 'sometimes|nullable|string|max:255', // Jika diizinkan update nama siswa dari sini
            // 'student_attendance_number' => 'sometimes|nullable|string|max:50'
        ]);
        // 'sometimes' berarti field hanya divalidasi jika ada dalam request

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = $validator->validated();
        
        // Jika ada data yang diupdate, tambahkan updatedAt
        if (!empty($updateData)) {
            $updateData['updatedAt'] = now()->toIso8601String();

            // Jika guru BK yang mengedit berbeda dari yang membuat awal (opsional, tergantung kebutuhan)
            // $currentGuruBkName = $authUser->name ?? ($this->firebase->findUserById($authUser->sub)['fields']['name']['stringValue'] ?? $authUser->username);
            // if (($existingCase['fields']['guruBkId']['stringValue'] ?? null) !== $authUser->sub) {
            //    $updateData['updatedByGuruBkId'] = $authUser->sub;
            //    $updateData['updatedByGuruBkName'] = $currentGuruBkName;
            // }
        } else {
            return response()->json(['message' => 'No data provided for update.'], 400);
        }


        // Anda perlu metode updateStudentCase di FirebaseService
        // $this->firebase->updateStudentCase($caseId, $updateData);
        // Metode updateDocument di FirebaseService mungkin perlu logika untuk hanya mengirim field yang ada di $updateData
        $this->firebase->updateJurnal($id, $updateData);


        // Ambil data terbaru setelah update untuk dikembalikan
        // $updatedCaseDoc = $this->firebase->getStudentCaseById($caseId);
        $updatedCaseDoc = $this->firebase->getJurnal($id);
        $fields = $updatedCaseDoc['fields'];
         $caseDataResponse = [
            'id' => basename($updatedCaseDoc['name']),
            'studentId' => $fields['studentId']['stringValue'] ?? null,
            'studentName' => $fields['studentName']['stringValue'] ?? 'N/A',
            'guruBkId' => $fields['guruBkId']['stringValue'] ?? null,
            'guruBkName' => $fields['guruBkName']['stringValue'] ?? 'N/A',
            'topic' => $fields['topic']['stringValue'] ?? '',
            'followUp' => $fields['followUp']['stringValue'] ?? '',
            'caseDate' => $fields['caseDate']['stringValue'] ?? null,
            'notes' => $fields['notes']['stringValue'] ?? null,
            'createdAt' => $fields['createdAt']['timestampValue'] ?? ($fields['createdAt']['stringValue'] ?? null),
            'updatedAt' => $fields['updatedAt']['timestampValue'] ?? ($fields['updatedAt']['stringValue'] ?? null),
        ];


        return response()->json(['message' => 'Student case updated successfully', 'data' => $caseDataResponse]);
    }

    /**
     * Remove the specified student case from storage.
     * Hanya bisa dilakukan oleh guru BK.
     */
    public function destroy(Request $request, $id)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($authUser->role !== 'guru_bk') {
            // Pertimbangkan apakah siswa boleh menghapus kasusnya sendiri (biasanya tidak untuk kasus formal)
            // Jika ya, tambahkan logika seperti di AspirationController
            return response()->json(['message' => 'Only counselors (Guru BK) can delete student cases.'], 403);
        }

        // Anda perlu metode getStudentCaseById di FirebaseService untuk memastikan kasus ada
        // $existingCase = $this->firebase->getStudentCaseById($caseId);
         $existingCase = $this->firebase->getJurnal($id);


        if (!$existingCase || isset($existingCase['error']) || !isset($existingCase['fields'])) {
            return response()->json(['message' => 'Student case not found.'], 404);
        }

        // Anda perlu metode deleteStudentCase di FirebaseService
        // $this->firebase->deleteStudentCase($caseId);
        $this->firebase->deleteJurnal($id);


        return response()->json(['message' => 'Student case deleted successfully'], 200);
    }

     /**
     * Helper untuk mendapatkan detail user dari Firebase (jika diperlukan lebih sering)
     * Ini bisa diletakkan di FirebaseService atau sebagai private method di sini jika hanya untuk controller ini.
     * Diasumsikan FirebaseService memiliki metode untuk mengambil user berdasarkan ID dari koleksi 'users'.
     */
    // private function getUserDetails($userId)
    // {
    //     if (!$userId) return null;
    //     // $userData = $this->firebase->getDocument('users/' . $userId); // Ganti 'users' dengan nama koleksi user Anda
    //     // if ($userData && isset($userData['fields'])) {
    //     //     return [
    //     //         'name' => $userData['fields']['name']['stringValue'] ?? null,
    //     //         'username' => $userData['fields']['username']['stringValue'] ?? null,
    //     //         // tambahkan field lain yang relevan
    //     //     ];
    //     // }
    //     return null;
    // }


     public function showSiswaRiwayatKasus()
    {
        // Jika Anda menggunakan session auth Laravel untuk melindungi halaman shell ini,
        // Anda bisa pass $authUser jika ada elemen UI yang membutuhkannya.
        // $authUser = Auth::user();
        // return view('kasus_siswa.index_siswa', ['authUser' => $authUser]);

        // Untuk konsistensi dengan pendekatan API-first, view hanya sebagai shell.
        // JavaScript di view akan menangani cek token dari localStorage untuk API calls.
        return view('kasus_siswa.index_siswa');
    }
}