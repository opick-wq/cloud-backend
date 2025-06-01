<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FirebaseService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
// For logging potential issues

class SiswaController extends Controller
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
            return null; // Tidak ada token atau format salah
        }
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            // Dekode token
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return $decoded; // Kembalikan payload token
        } catch (\Exception $e) {
            return null; // Token tidak valid
        }
    }
    // ... (Your existing extractPersonDataFromFirestore, extractSaudaraKandungFromFirestore, etc. methods remain unchanged) ...
    private function extractPersonDataFromFirestore($personField)
    {
        $defaultPersonData = [
            'nama' => '', 'tanggal_lahir' => '', 'agama' => '',
            'pendidikan' => '', 'pekerjaan' => '', 'suku_bangsa' => '', 'alamat' => '',
        ];

        if (is_null($personField) || !isset($personField['mapValue']['fields'])) {
            return $defaultPersonData;
        }
        $fields = $personField['mapValue']['fields'];
        return [
            'nama' => $fields['nama']['stringValue'] ?? '',
            'tanggal_lahir' => isset($fields['tanggal_lahir']['timestampValue'])
                ? date('Y-m-d', strtotime($fields['tanggal_lahir']['timestampValue']))
                : '',
            'agama' => $fields['agama']['stringValue'] ?? '',
            'pendidikan' => $fields['pendidikan']['stringValue'] ?? '',
            'pekerjaan' => $fields['pekerjaan']['stringValue'] ?? '',
            'suku_bangsa' => $fields['suku_bangsa']['stringValue'] ?? '',
            'alamat' => $fields['alamat']['stringValue'] ?? '',
        ];
    }

    private function extractSaudaraKandungFromFirestore($saudaraArrayField)
    {
        $saudaraList = [];
        if (is_null($saudaraArrayField) || !isset($saudaraArrayField['arrayValue']['values'])) {
            return $saudaraList;
        }

        foreach ($saudaraArrayField['arrayValue']['values'] as $saudaraValue) {
            if (isset($saudaraValue['mapValue']['fields'])) {
                $f = $saudaraValue['mapValue']['fields'];
                $saudaraList[] = [
                    'nama' => $f['nama']['stringValue'] ?? '',
                    'tanggal_lahir' => isset($f['tanggal_lahir']['timestampValue'])
                        ? date('Y-m-d', strtotime($f['tanggal_lahir']['timestampValue']))
                        : '',
                    'jenis_kelamin' => $f['jenis_kelamin']['stringValue'] ?? '',
                    'status_hubungan' => $f['status_hubungan']['stringValue'] ?? '',
                    'pekerjaan_sekolah' => $f['pekerjaan_sekolah']['stringValue'] ?? '',
                    'tingkat' => $f['tingkat']['stringValue'] ?? '',
                    'status_perkawinan' => $f['status_perkawinan']['stringValue'] ?? '',
                ];
            }
        }
        return $saudaraList;
    }

    private function extractSakitKerasFromFirestore($sakitKerasArrayField)
    {
        $sakitList = [];
        if (is_null($sakitKerasArrayField) || !isset($sakitKerasArrayField['arrayValue']['values'])) {
            return $sakitList;
        }

        foreach ($sakitKerasArrayField['arrayValue']['values'] as $sakitValue) {
            if (isset($sakitValue['mapValue']['fields'])) {
                $f = $sakitValue['mapValue']['fields'];
                $sakitList[] = [
                    'jenis_penyakit' => $f['jenis_penyakit']['stringValue'] ?? '',
                    'usia_saat_sakit' => isset($f['usia_saat_sakit']['integerValue']) ? (int)$f['usia_saat_sakit']['integerValue'] : ($f['usia_saat_sakit']['stringValue'] ?? null),
                    'opname' => $f['opname']['stringValue'] ?? '',
                    'opname_di_rs' => $f['opname_di_rs']['stringValue'] ?? '',
                ];
            }
        }
        return $sakitList;
    }

    private function extractPrestasiFromFirestore($prestasiArrayField)
    {
        $prestasiList = [];
        if (is_null($prestasiArrayField) || !isset($prestasiArrayField['arrayValue']['values'])) {
            return $prestasiList;
        }

        foreach ($prestasiArrayField['arrayValue']['values'] as $prestasiValue) {
            if (isset($prestasiValue['mapValue']['fields'])) {
                $f = $prestasiValue['mapValue']['fields'];
                $prestasiList[] = [
                    'nama_kejuaraan' => $f['nama_kejuaraan']['stringValue'] ?? '',
                    'tingkat' => $f['tingkat']['stringValue'] ?? '',
                    'raihan_prestasi' => $f['raihan_prestasi']['stringValue'] ?? '',
                    'tahun_kelas' => $f['tahun_kelas']['stringValue'] ?? '',
                ];
            }
        }
        return $prestasiList;
    }

    private function extractFirestoreStringArray($arrayField)
    {
        $result = [];
        if (is_null($arrayField) || !isset($arrayField['arrayValue']['values'])) {
            return $result;
        }
        foreach ($arrayField['arrayValue']['values'] as $value) {
            $result[] = $value['stringValue'] ?? '';
        }
        return $result;
    }


    public function index()
{
    if (!isset($this->firebase)) {
        return response()->json(['error' => 'Firebase service not configured.'], 500);
    }

    $response = $this->firebase->getAll('Users');
    $siswaList = [];

    if (isset($response['documents'])) {
        foreach ($response['documents'] as $doc) {
            if (!isset($doc['fields'])) continue;

            // Cek apakah role adalah 'siswa'
            if (
                isset($doc['fields']['role']) &&
                isset($doc['fields']['role']['stringValue']) &&
                $doc['fields']['role']['stringValue'] === 'siswa'
            ) {
                $siswaList[] = $this->formatSiswaData($doc); // Gunakan helper
            }
        }
    }

    return response()->json($siswaList);
}


    // NEW: Helper function to format Siswa data from Firestore doc
    private function formatSiswaData($doc)
    {
        if (!isset($doc['fields'])) return null;
        $fields = $doc['fields'];

        return [
            'id' => basename($doc['name']),
            'name' => $fields['name']['stringValue'] ?? '',
            'username' => $fields['username']['stringValue'] ?? '',
            'jenis_kelamin' => $fields['jenis_kelamin']['stringValue'] ?? '',
            'tempat_lahir' => $fields['tempat_lahir']['stringValue'] ?? '',
            'tanggal_lahir' => isset($fields['tanggal_lahir']['timestampValue'])
                ? date('Y-m-d', strtotime($fields['tanggal_lahir']['timestampValue']))
                : '',
            'agama' => $fields['agama']['stringValue'] ?? '',
            'suku_bangsa' => $fields['suku_bangsa']['stringValue'] ?? '',
            'tanggal_masuk' => isset($fields['tanggal_masuk']['timestampValue'])
                ? date('Y-m-d', strtotime($fields['tanggal_masuk']['timestampValue']))
                : '',
            'asal_sekolah' => $fields['asal_sekolah']['stringValue'] ?? '',
            'status_sebagai' => $fields['status_sebagai']['stringValue'] ?? '',

            'alamat_asal' => $fields['alamat_asal']['stringValue'] ?? '',
            'nomor_telp_hp' => $fields['nomor_telp_hp']['stringValue'] ?? '',
            'termasuk_daerah_asal' => $this->extractFirestoreStringArray($fields['termasuk_daerah_asal'] ?? null),
            'alamat_sekarang' => $fields['alamat_sekarang']['stringValue'] ?? '',
            'nomor_telp_hp_sekarang' => $fields['nomor_telp_hp_sekarang']['stringValue'] ?? '',
            'termasuk_daerah_sekarang' => $this->extractFirestoreStringArray($fields['termasuk_daerah_sekarang'] ?? null),
            'jarak_rumah_sekolah' => isset($fields['jarak_rumah_sekolah']['integerValue']) ? (int)$fields['jarak_rumah_sekolah']['integerValue'] : ($fields['jarak_rumah_sekolah']['stringValue'] ?? null),
            'alat_sarana_ke_sekolah' => $this->extractFirestoreStringArray($fields['alat_sarana_ke_sekolah'] ?? null),
             // Assuming you might have a _lainnya_text field stored separately
            'alat_sarana_ke_sekolah_lainnya_text' => $fields['alat_sarana_ke_sekolah_lainnya_text']['stringValue'] ?? '',
            'tempat_tinggal' => $fields['tempat_tinggal']['stringValue'] ?? '',
            'tempat_tinggal_lainnya_text' => $fields['tempat_tinggal_lainnya_text']['stringValue'] ?? '', // Added for "Lainnya"
            'tinggal_bersama' => $this->extractFirestoreStringArray($fields['tinggal_bersama'] ?? null),
            'tinggal_bersama_wali_text' => $fields['tinggal_bersama_wali_text']['stringValue'] ?? '', // Added for "Wali (teks)"
            'tinggal_bersama_lainnya_text' => $fields['tinggal_bersama_lainnya_text']['stringValue'] ?? '', // Added for "Lainnya (teks)"
            'rumah_terbuat_dari' => $fields['rumah_terbuat_dari']['stringValue'] ?? '',
            'rumah_terbuat_dari_lainnya_text' => $fields['rumah_terbuat_dari_lainnya_text']['stringValue'] ?? '', // Added for "Lainnya"
            'alat_fasilitas_dimiliki' => $this->extractFirestoreStringArray($fields['alat_fasilitas_dimiliki'] ?? null),
            'alat_fasilitas_dimiliki_surat_kabar_text' => $fields['alat_fasilitas_dimiliki_surat_kabar_text']['stringValue'] ?? '', // Added


            'data_keluarga' => [
                'ayah' => $this->extractPersonDataFromFirestore($fields['data_keluarga']['mapValue']['fields']['ayah'] ?? null),
                'ibu' => $this->extractPersonDataFromFirestore($fields['data_keluarga']['mapValue']['fields']['ibu'] ?? null),
                'wali' => $this->extractPersonDataFromFirestore($fields['data_keluarga']['mapValue']['fields']['wali'] ?? null),
            ],
            'anak_ke' => isset($fields['anak_ke']['integerValue']) ? (int)$fields['anak_ke']['integerValue'] : ($fields['anak_ke']['stringValue'] ?? null),
            'saudara_kandung' => $this->extractSaudaraKandungFromFirestore($fields['saudara_kandung'] ?? null),

            'tinggi_badan' => isset($fields['tinggi_badan']['integerValue']) ? (int)$fields['tinggi_badan']['integerValue'] : ($fields['tinggi_badan']['stringValue'] ?? null),
            'berat_badan' => isset($fields['berat_badan']['doubleValue']) ? (float)$fields['berat_badan']['doubleValue'] : (isset($fields['berat_badan']['integerValue']) ? (int)$fields['berat_badan']['integerValue'] : ($fields['berat_badan']['stringValue'] ?? null)),
            'golongan_darah' => $fields['golongan_darah']['stringValue'] ?? '',
            'bentuk_mata' => $fields['bentuk_mata']['stringValue'] ?? '',
            'bentuk_muka' => $fields['bentuk_muka']['stringValue'] ?? '',
            'rambut' => $fields['rambut']['stringValue'] ?? '',
            'warna_kulit' => $fields['warna_kulit']['stringValue'] ?? '',
            'memiliki_cacat_tubuh' => $fields['memiliki_cacat_tubuh']['stringValue'] ?? '',
            'cacat_tubuh_penjelasan' => $fields['cacat_tubuh_penjelasan']['stringValue'] ?? '',
            'memakai_kacamata' => $fields['memakai_kacamata']['stringValue'] ?? '',
            'kacamata_kelainan' => $fields['kacamata_kelainan']['stringValue'] ?? '',
            'sakit_sering_diderita' => $fields['sakit_sering_diderita']['stringValue'] ?? '',
            'sakit_keras' => $this->extractSakitKerasFromFirestore($fields['sakit_keras'] ?? null),

            'kemampuan_bahasa_indonesia' => $fields['kemampuan_bahasa_indonesia']['stringValue'] ?? '',
            'bahasa_sehari_hari_dirumah' => $fields['bahasa_sehari_hari_dirumah']['stringValue'] ?? '',
            'bahasa_daerah_dikuasai' => $this->extractFirestoreStringArray($fields['bahasa_daerah_dikuasai'] ?? null),
            'bahasa_daerah_lainnya_text' => $fields['bahasa_daerah_lainnya_text']['stringValue'] ?? '',
            'bahasa_asing_dikuasai' => $this->extractFirestoreStringArray($fields['bahasa_asing_dikuasai'] ?? null),
            'bahasa_asing_lainnya_text' => $fields['bahasa_asing_lainnya_text']['stringValue'] ?? '',

            'hobby' => $fields['hobby']['stringValue'] ?? '',
            'cita_cita' => $fields['cita_cita']['stringValue'] ?? '',

            'pelajaran_disukai_sd' => $fields['pelajaran_disukai_sd']['stringValue'] ?? '',
            'alasan_pelajaran_disukai_sd' => $fields['alasan_pelajaran_disukai_sd']['stringValue'] ?? '',
            'pelajaran_tidak_disukai_sd' => $fields['pelajaran_tidak_disukai_sd']['stringValue'] ?? '',
            'alasan_pelajaran_tidak_disukai_sd' => $fields['alasan_pelajaran_tidak_disukai_sd']['stringValue'] ?? '',
            'prestasi_sd' => $this->extractPrestasiFromFirestore($fields['prestasi_sd'] ?? null),
            'kegiatan_belajar_dirumah' => $fields['kegiatan_belajar_dirumah']['stringValue'] ?? '',
            'dilaksanakan_setiap_belajar' => $this->extractFirestoreStringArray($fields['dilaksanakan_setiap_belajar'] ?? null),
            'kesulitan_belajar' => $fields['kesulitan_belajar']['stringValue'] ?? '',
            'hambatan_belajar' => $fields['hambatan_belajar']['stringValue'] ?? '',
            'prestasi_smp' => $this->extractPrestasiFromFirestore($fields['prestasi_smp'] ?? null),
        ];
    }

    public function store(Request $request)
    {
        // Your existing validation, make sure to include _lainnya_text fields from below
        $validatedData = $this->validateSiswaData($request); // Use helper for validation rules
        $id = (string) Str::uuid();
        $preparedData = $this->prepareDataForFirebase(array_merge(['id' => $id], $validatedData));

        $this->firebase->create($preparedData, 'Users');
        return response()->json(['message' => 'Data siswa berhasil ditambahkan'], 201);
    }

    // NEW: Method to show the edit form
   public function edit($id)
    {
        if (!isset($this->firebase)) {
            // For a web interface, you might redirect with an error or show an error view
            // For simplicity here, we'll stick to the JSON error if Firebase isn't set up,
            // though ideally, this check would be handled earlier or differently for web routes.
            return response("Firebase service not configured. Please check server logs.", 500); // Or redirect()->route('some.error.page');
        }

        $doc = $this->firebase->get($id, 'Users');

        if (!isset($doc['name']) || !isset($doc['fields'])) { // Check 'name' for document path
            // For a web route, you'd typically abort or redirect with a message
            abort(404, 'Data Siswa tidak ditemukan.'); // This will show Laravel's 404 page
        }

        $siswa = $this->formatSiswaData($doc);

        if (is_null($siswa)) {
            // For a web route, handle this error appropriately, e.g., log and show a generic error
            Log::error("Gagal memformat data siswa dengan ID: {$id}");
            abort(500, 'Terjadi kesalahan saat memproses data siswa.');
        }
        
        // For a web route, return the view with the siswa data:
        return view('siswa.edit', compact('siswa'));

        // Comment out or remove the API context return:
        // return response()->json($siswa); 
    }
    
    // Your existing show method, can be simplified using formatSiswaData
    public function show($id)
    {
        if (!isset($this->firebase)) {
            return response()->json(['error' => 'Firebase service not configured.'], 500);
        }
        $doc = $this->firebase->get($id, 'Users');

        if (!isset($doc['name']) || !isset($doc['fields'])) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }
        
        $siswa = $this->formatSiswaData($doc);

        if (is_null($siswa)) {
             return response()->json(['message' => 'Gagal memformat data siswa.'], 500);
        }
        return response()->json($siswa);
    }

    public function showProfile(Request $request)
{
    $authUser = $this->getAuthUser($request);

    if (!$authUser || !isset($authUser->sub)) {
        return response()->json(['message' => 'Unauthorized or invalid token.'], 401);
    }

    if (!$this->firebase) {
        return response()->json(['error' => 'Firebase service not configured.'], 500);
    }

    try {
        $doc = $this->firebase->get($authUser->sub, 'Users');
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal mengambil data dari Firebase.', 'details' => $e->getMessage()], 500);
    }

    if (empty($doc['name']) || empty($doc['fields'])) {
        return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
    }

    $siswa = $this->formatSiswaData($doc);

    if (is_null($siswa)) {
        return response()->json(['message' => 'Gagal memformat data siswa.'], 500);
    }

    return response()->json($siswa);
}




    // Helper for validation rules (to avoid repetition in store and update)
    private function validateSiswaData(Request $request, $siswaId = null)
    {
        $usernameRule = 'nullable|string|max:255';
        if ($siswaId) {
            // Potentially add unique rule for username if your FirebaseService doesn't handle it
            // For Firestore, unique checks are often done at the application level or with security rules + cloud functions
            // $usernameRule = ['nullable', 'string', 'max:255', Rule::unique('users_collection_name_in_firestore', 'username')->ignore($siswaId, 'firebase_document_id_field_name')];
        }

        return $request->validate([
            'name' => 'nullable|string|max:255',
            'username' => $usernameRule,
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date_format:Y-m-d',
            'agama' => 'nullable|string|max:100',
            'suku_bangsa' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date_format:Y-m-d',
            'asal_sekolah' => 'nullable|string|max:255',
            'status_sebagai' => 'nullable|in:Siswa Baru,Pindahan',

            'alamat_asal' => 'nullable|string',
            'nomor_telp_hp' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/',
            'termasuk_daerah_asal' => 'nullable|array',
            'termasuk_daerah_asal.*' => 'in:Dalam kota,Pinggir kota,Luar kota,Pinggir sungai,Daerah pegunungan',
            'alamat_sekarang' => 'nullable|string',
            'nomor_telp_hp_sekarang' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/',
            'termasuk_daerah_sekarang' => 'nullable|array',
            'termasuk_daerah_sekarang.*' => 'in:Dalam kota,Pinggir kota,Luar kota,Pinggir sungai,Daerah pegunungan',
            'jarak_rumah_sekolah' => 'nullable|integer|min:0',
            'alat_sarana_ke_sekolah' => 'nullable|array',
            'alat_sarana_ke_sekolah.*' => 'in:Jalan kaki,Naik sepeda,Naik sepeda motor,Diantar orang tua,Naik taksi/ojek,Naik mobil pribadi,Lainnya (teks)',
            'alat_sarana_ke_sekolah_lainnya_text' => ['nullable','string','max:255', Rule::requiredIf(fn () => is_array($request->input('alat_sarana_ke_sekolah')) && in_array('Lainnya (teks)', $request->input('alat_sarana_ke_sekolah')))],
            
            'tempat_tinggal' => 'nullable|string|in:Rumah sendiri,Rumah dinas,Rumah kontrakan,Rumah nenek/kakek,Kamar kost,Lainnya (teks)',
            'tempat_tinggal_lainnya_text' => ['nullable','string','max:255', Rule::requiredIf(fn () => $request->input('tempat_tinggal') === 'Lainnya (teks)')],

            'tinggal_bersama' => 'nullable|array',
            'tinggal_bersama.*' => 'in:Ayah dan ibu kandung,Ayah kandung dan ibu tiri,Ayah tiri dan ibu kandung,Ayah kandung saja,Ibu kandung saja,Nenek/Kakek,Saudara kandung,Sendiri,Wali (teks),Lainnya (teks)',
            'tinggal_bersama_wali_text' => ['nullable','string','max:255', Rule::requiredIf(fn () => is_array($request->input('tinggal_bersama')) && in_array('Wali (teks)', $request->input('tinggal_bersama')))],
            'tinggal_bersama_lainnya_text' => ['nullable','string','max:255', Rule::requiredIf(fn () => is_array($request->input('tinggal_bersama')) && in_array('Lainnya (teks)', $request->input('tinggal_bersama')))],
            
            'rumah_terbuat_dari' => 'nullable|string|in:Tembok beton,Setengah kayu,Kayu,Bambu,Lainnya (teks)',
            'rumah_terbuat_dari_lainnya_text' => ['nullable','string','max:255', Rule::requiredIf(fn () => $request->input('rumah_terbuat_dari') === 'Lainnya (teks)')],

            'alat_fasilitas_dimiliki' => 'nullable|array',
            'alat_fasilitas_dimiliki.*' => 'in:Kamar sendiri,Ruang belajar sendiri,Perpustakaan keluarga,Radio/TV/parabola,Ruang tamu,Almari pribadi,Gitar/piano alat musik,Komputer/laptop/LCD,Kompor/kompor gas,Ruang makan sendiri,Almari es,Sepeda,Sepeda motor,Mobil,Berlangganan surat kabar/majalah (teks)',
            'alat_fasilitas_dimiliki_surat_kabar_text' => ['nullable','string','max:255', Rule::requiredIf(fn () => is_array($request->input('alat_fasilitas_dimiliki')) && in_array('Berlangganan surat kabar/majalah (teks)', $request->input('alat_fasilitas_dimiliki')))],

            'data_keluarga' => 'nullable|array',
            'data_keluarga.ayah.nama' => 'nullable|string|max:255',
            'data_keluarga.ayah.tanggal_lahir' => 'nullable|date_format:Y-m-d',
            'data_keluarga.ayah.agama' => 'nullable|string|max:100',
            'data_keluarga.ayah.pendidikan' => 'nullable|string|max:255',
            'data_keluarga.ayah.pekerjaan' => 'nullable|string|max:255',
            'data_keluarga.ayah.suku_bangsa' => 'nullable|string|max:255',
            'data_keluarga.ayah.alamat' => 'nullable|string',
            'data_keluarga.ibu.nama' => 'nullable|string|max:255',
            'data_keluarga.ibu.tanggal_lahir' => 'nullable|date_format:Y-m-d',
            'data_keluarga.ibu.agama' => 'nullable|string|max:100',
            'data_keluarga.ibu.pendidikan' => 'nullable|string|max:255',
            'data_keluarga.ibu.pekerjaan' => 'nullable|string|max:255',
            'data_keluarga.ibu.suku_bangsa' => 'nullable|string|max:255',
            'data_keluarga.ibu.alamat' => 'nullable|string',
            'data_keluarga.wali.nama' => 'nullable|string|max:255',
            'data_keluarga.wali.tanggal_lahir' => 'nullable|date_format:Y-m-d',
            'data_keluarga.wali.agama' => 'nullable|string|max:100',
            'data_keluarga.wali.pendidikan' => 'nullable|string|max:255',
            'data_keluarga.wali.pekerjaan' => 'nullable|string|max:255',
            'data_keluarga.wali.suku_bangsa' => 'nullable|string|max:255',
            'data_keluarga.wali.alamat' => 'nullable|string',

            'anak_ke' => 'nullable|integer|min:1',

            'saudara_kandung' => 'nullable|array',
            'saudara_kandung.*.nama' => 'nullable|string|max:255',
            'saudara_kandung.*.tanggal_lahir' => 'nullable|date_format:Y-m-d',
            'saudara_kandung.*.jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'saudara_kandung.*.status_hubungan' => 'nullable|in:Kandung,Siri',
            'saudara_kandung.*.pekerjaan_sekolah' => 'nullable|string|max:255',
            'saudara_kandung.*.tingkat' => 'nullable|string|max:255',
            'saudara_kandung.*.status_perkawinan' => 'nullable|in:Kawin,Belum',

            'tinggi_badan' => 'nullable|integer|min:0',
            'berat_badan' => 'nullable|numeric|min:0', // Changed to numeric for potential decimals
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'bentuk_mata' => 'nullable|string|max:255',
            'bentuk_muka' => 'nullable|string|max:255',
            'rambut' => 'nullable|in:Lurus,Keriting,Bergelombang',
            'warna_kulit' => 'nullable|string|max:255',
            'memiliki_cacat_tubuh' => 'nullable|in:Ya,Tidak',
            'cacat_tubuh_penjelasan' => 'nullable|string|required_if:memiliki_cacat_tubuh,Ya',
            'memakai_kacamata' => 'nullable|in:Ya,Tidak',
            'kacamata_kelainan' => 'nullable|string|max:255|required_if:memakai_kacamata,Ya',
            'sakit_sering_diderita' => 'nullable|string',

            'sakit_keras' => 'nullable|array',
            'sakit_keras.*.jenis_penyakit' => 'nullable|string|max:255',
            'sakit_keras.*.usia_saat_sakit' => 'nullable|integer|min:0',
            'sakit_keras.*.opname' => 'nullable|in:Ya,Tidak',
            'sakit_keras.*.opname_di_rs' => 'nullable|string|max:255|required_if:sakit_keras.*.opname,Ya',

            'kemampuan_bahasa_indonesia' => 'nullable|in:Menguasai,Cukup Menguasai,Kurang Menguasai,Tidak Menguasai',
            'bahasa_sehari_hari_dirumah' => 'nullable|string|max:255',
            'bahasa_daerah_dikuasai' => 'nullable|array',
            'bahasa_daerah_dikuasai.*' => 'in:Bahasa Banjar,Bahasa Dayak,Bahasa Jawa,Bahasa Ambon,Lainnya',
            'bahasa_daerah_lainnya_text' => ['nullable','string','max:255', Rule::requiredIf(fn () => is_array($request->input('bahasa_daerah_dikuasai')) && in_array('Lainnya', $request->input('bahasa_daerah_dikuasai')))],
            'bahasa_asing_dikuasai' => 'nullable|array',
            'bahasa_asing_dikuasai.*' => 'in:Bahasa Inggris,Bahasa Arab,Bahasa Mandarin,Bahasa Jerman,Lainnya',
            'bahasa_asing_lainnya_text' => ['nullable','string','max:255', Rule::requiredIf(fn () => is_array($request->input('bahasa_asing_dikuasai')) && in_array('Lainnya', $request->input('bahasa_asing_dikuasai')))],

            'hobby' => 'nullable|string',
            'cita_cita' => 'nullable|string',

            'pelajaran_disukai_sd' => 'nullable|string|max:255',
            'alasan_pelajaran_disukai_sd' => 'nullable|string',
            'pelajaran_tidak_disukai_sd' => 'nullable|string|max:255',
            'alasan_pelajaran_tidak_disukai_sd' => 'nullable|string',
            'prestasi_sd' => 'nullable|array',
            'prestasi_sd.*.nama_kejuaraan' => 'nullable|string|max:255',
            'prestasi_sd.*.tingkat' => 'nullable|string|max:255',
            'prestasi_sd.*.raihan_prestasi' => 'nullable|string|max:255',
            'prestasi_sd.*.tahun_kelas' => 'nullable|string|max:255',

            'kegiatan_belajar_dirumah' => 'nullable|in:Rutin,Tidak',
            'dilaksanakan_setiap_belajar' => 'nullable|array',
            'dilaksanakan_setiap_belajar.*' => 'in:Sore,Malam,Pagi',
            'kesulitan_belajar' => 'nullable|string',
            'hambatan_belajar' => 'nullable|string',

            'prestasi_smp' => 'nullable|array',
            'prestasi_smp.*.nama_kejuaraan' => 'nullable|string|max:255',
            'prestasi_smp.*.tingkat' => 'nullable|string|max:255',
            'prestasi_smp.*.raihan_prestasi' => 'nullable|string|max:255',
            'prestasi_smp.*.tahun_kelas' => 'nullable|string|max:255',
        ]);
    }

    // Helper function to prepare data before sending to Firebase
    // This is important if your FirebaseService expects specific data types (e.g. Firestore Timestamps)
    // For now, we assume dates are sent as 'Y-m-d' strings and numbers as correct types from validation.
    // If your FirebaseService handles this, this function might be simpler.
    private function prepareDataForFirebase(array $data): array
    {
        // Example: Convert date strings to Firestore Timestamp objects if needed by your service
        // This depends heavily on your FirebaseService implementation
        // foreach (['tanggal_lahir', 'tanggal_masuk', 'data_keluarga.ayah.tanggal_lahir', ...] as $dateFieldPath) {
        //    $value = data_get($data, $dateFieldPath);
        //    if ($value) {
        //        try {
        //             // If your service expects Firestore Timestamp
        //             // data_set($data, $dateFieldPath, new \Google\Cloud\Firestore\Timestamp(new \DateTime($value)));
        //             // If your service expects ISO string
        //             data_set($data, $dateFieldPath, (new \DateTime($value))->format(\DateTime::RFC3339_EXTENDED));
        //        } catch (\Exception $e) {
        //            Log::error("Error formatting date {$dateFieldPath}: {$value} - " . $e->getMessage());
        //            // Decide how to handle: unset, set to null, or keep as is
        //            data_set($data, $dateFieldPath, null);
        //        }
        //    }
        // }

        // Ensure numeric types for specific fields if validation allows strings from form
        $numericFields = ['jarak_rumah_sekolah', 'anak_ke', 'tinggi_badan', 'berat_badan', 'sakit_keras.*.usia_saat_sakit'];
        foreach ($numericFields as $field) {
            if (Str::contains($field, '.*.')) { // Handles array of objects like sakit_keras.*.usia_saat_sakit
                [$arrayName, $propName] = explode('.*.', $field);
                if (isset($data[$arrayName]) && is_array($data[$arrayName])) {
                    foreach ($data[$arrayName] as $key => $item) {
                        if (isset($item[$propName]) && $item[$propName] !== '' && $item[$propName] !== null) {
                            $data[$arrayName][$key][$propName] = is_numeric($item[$propName]) ? ($propName === 'berat_badan' ? (float)$item[$propName] : (int)$item[$propName]) : null;
                        } elseif (isset($item[$propName])) {
                            $data[$arrayName][$key][$propName] = null; // Set to null if empty or not set
                        }
                    }
                }
            } else {
                if (isset($data[$field]) && $data[$field] !== '' && $data[$field] !== null) {
                    $data[$field] = is_numeric($data[$field]) ? ($field === 'berat_badan' ? (float)$data[$field] : (int)$data[$field]) : null;
                } elseif (isset($data[$field])) {
                     $data[$field] = null; // Set to null if empty or not set
                }
            }
        }
        
        // Remove _lainnya_text fields if their corresponding "Lainnya" option is not selected
        // This prevents storing empty text fields if "Lainnya" was unchecked.
        $lainnyaChecks = [
            'alat_sarana_ke_sekolah' => 'alat_sarana_ke_sekolah_lainnya_text',
            'tempat_tinggal' => 'tempat_tinggal_lainnya_text', // This is a radio/select, slightly different logic
            'tinggal_bersama' => ['tinggal_bersama_wali_text', 'tinggal_bersama_lainnya_text'],
            'rumah_terbuat_dari' => 'rumah_terbuat_dari_lainnya_text', // Radio/select
            'alat_fasilitas_dimiliki' => 'alat_fasilitas_dimiliki_surat_kabar_text',
            'bahasa_daerah_dikuasai' => 'bahasa_daerah_lainnya_text',
            'bahasa_asing_dikuasai' => 'bahasa_asing_lainnya_text',
        ];

        foreach ($lainnyaChecks as $mainField => $textFieldOrFields) {
            $textFields = (array) $textFieldOrFields;
            foreach ($textFields as $textField) {
                $lainnyaValue = ''; // Determine the "Lainnya" value for the main field
                if ($mainField === 'tempat_tinggal' || $mainField === 'rumah_terbuat_dari') {
                    $lainnyaValue = 'Lainnya (teks)';
                    if (!isset($data[$mainField]) || $data[$mainField] !== $lainnyaValue) {
                        unset($data[$textField]);
                    }
                } elseif ($mainField === 'tinggal_bersama') {
                     if ($textField === 'tinggal_bersama_wali_text') $lainnyaValue = 'Wali (teks)';
                     if ($textField === 'tinggal_bersama_lainnya_text') $lainnyaValue = 'Lainnya (teks)';

                     if (!isset($data[$mainField]) || !is_array($data[$mainField]) || !in_array($lainnyaValue, $data[$mainField])) {
                        unset($data[$textField]);
                    }
                } elseif ($mainField === 'alat_fasilitas_dimiliki' && $textField === 'alat_fasilitas_dimiliki_surat_kabar_text') {
                    $lainnyaValue = 'Berlangganan surat kabar/majalah (teks)';
                     if (!isset($data[$mainField]) || !is_array($data[$mainField]) || !in_array($lainnyaValue, $data[$mainField])) {
                        unset($data[$textField]);
                    }
                }
                 else { // Default for checkbox arrays
                    $lainnyaValue = 'Lainnya (teks)'; // Or specific value like 'Lainnya' if validation uses that
                     if (!isset($data[$mainField]) || !is_array($data[$mainField]) || !in_array($lainnyaValue, $data[$mainField])) {
                        unset($data[$textField]);
                    }
                }
            }
        }


        // Ensure arrays of objects are cleaned (remove empty sub-arrays if all their fields are null/empty)
        // e.g. if a 'saudara_kandung' entry is submitted with all empty fields.
        $arraysOfObjects = ['saudara_kandung', 'sakit_keras', 'prestasi_sd', 'prestasi_smp'];
        foreach ($arraysOfObjects as $arrKey) {
            if (isset($data[$arrKey]) && is_array($data[$arrKey])) {
                $data[$arrKey] = array_filter($data[$arrKey], function ($item) {
                    if (!is_array($item)) return false; // Should not happen with form submission
                    foreach ($item as $value) {
                        if ($value !== null && $value !== '') {
                            return true; // Keep if at least one field has value
                        }
                    }
                    return false; // Discard if all fields are null/empty
                });
                 $data[$arrKey] = array_values($data[$arrKey]); // Re-index array
            }
        }


        return $data;
    }


    public function update(Request $request, $id)
    {
        // Fetch existing document to ensure it exists
        $existingDoc = $this->firebase->get($id, 'Users');
        if (!isset($existingDoc['name'])) { // Check 'name' for document path
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        $validatedData = $this->validateSiswaData($request, $id);
        $preparedData = $this->prepareDataForFirebase($validatedData);

        if (isset($this->firebase)) {
            $this->firebase->update($id, $preparedData, 'Users');
        } else {
            Log::error('Firebase service not initialized in SiswaController@update.');
            return response()->json(['message' => 'Data validation passed, but Firebase service not configured.'], 500);
        }

        return response()->json(['message' => 'Data siswa berhasil diperbarui']);
    }

    public function destroy($id)
    {
        // Consider checking if doc exists before deleting
        $this->firebase->delete($id, 'Users');
        return response()->json(['message' => 'Data siswa berhasil dihapus']);
    }

    // Your existing formatCheckboxValue, can be removed if extractFirestoreStringArray is used everywhere
    // protected function formatCheckboxValue(array $values): array
    // {
    //     $formattedValues = [];
    //     foreach ($values as $value) {
    //         $formattedValues[] = $value['stringValue'] ?? null;
    //     }
    //     return $formattedValues;
    // }


     public function chatguru()
{
    if (!isset($this->firebase)) {
        return response()->json(['error' => 'Firebase service not configured.'], 500);
    }

    $response = $this->firebase->getAll('Users');
    $guruList = [];

    if (isset($response['documents'])) {
        foreach ($response['documents'] as $doc) {
            if (!isset($doc['fields'])) continue;

            // Cek apakah role adalah 'siswa'
            if (
                isset($doc['fields']['role']) &&
                isset($doc['fields']['role']['stringValue']) &&
                $doc['fields']['role']['stringValue'] === 'guru_bk'
            ) {
                $guruList[] = $this->formatChatData($doc); // Gunakan helper
            }
        }
    }

    return response()->json($guruList);
}

private function formatChatData($doc)
    {
        if (!isset($doc['fields'])) return null;
        $fields = $doc['fields'];

        return [
            'id' => basename($doc['name']),
            'name' => $fields['name']['stringValue'] ?? '',
            'avatarUrl' => $fields['avatarUrl']['stringValue'] ?? '',
    ];
    }

    public function updateProfile(Request $request)
{
    $authUser = $this->getAuthUser($request);

    if (!$authUser || !isset($authUser->sub)) {
        return response()->json(['message' => 'Unauthorized or invalid token.'], 401);
    }

    $id = $authUser->sub; // Gunakan UID dari token

    if (!isset($this->firebase)) {
        Log::error('Firebase service not initialized in SiswaController@updateProfile.');
        return response()->json(['message' => 'Firebase service not configured.'], 500);
    }

    // Cek apakah dokumen siswa ada
    $existingDoc = $this->firebase->get($id, 'Users');
    if (!isset($existingDoc['name'])) {
        return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
    }

    // Validasi dan persiapkan data
    $validatedData = $this->validateSiswaData($request, $id);
    $preparedData = $this->prepareDataForFirebase($validatedData);

    try {
        $this->firebase->update($id, $preparedData, 'Users');
    } catch (\Exception $e) {
        Log::error("Gagal memperbarui data siswa: " . $e->getMessage());
        return response()->json(['message' => 'Terjadi kesalahan saat memperbarui data.'], 500);
    }

    return response()->json(['message' => 'Data siswa berhasil diperbarui']);
}

}