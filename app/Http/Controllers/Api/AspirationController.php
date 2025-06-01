<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;

class AspirationController extends Controller
{
    protected $firebase;
    protected $jwtSecret;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
        $this->jwtSecret = env('JWT_SECRET');
    }

    // Metode untuk mengambil dan memvalidasi user dari token JWT secara manual
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

    /**
     * Store a newly created aspiration in storage.
     * Hanya bisa dilakukan oleh siswa.
     */
    public function store(Request $request)
    {
        $authUser = $this->getAuthUser($request); // Pengecekan token manual
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Hanya siswa yang boleh membuat aspirasi
        if ($authUser->role !== 'siswa') {
            return response()->json(['message' => 'Only students can submit aspirations.'], 403);
        }

        $data = $request->validate([
            'content' => 'required|string|max:255'
        ]);

        $aspirationId = (string) Str::uuid();
        $isAnonymous = $request->input('is_anonymous', false);

        $aspirationData = [
            'id' => $aspirationId, // Simpan UUID sebagai field 'id'
            'userId' => $authUser->sub,
            'username' => $isAnonymous ? 'Anonymous' : $authUser->username,
            'name' => $isAnonymous ? 'Siswa Anonim' : ($this->firebase->findByUsername($authUser->username)['fields']['name']['stringValue'] ?? $authUser->username), // Ambil nama lengkap jika tidak anonim
            'content' => $data['content'],
            'createdAt' => now()->toIso8601String(),
            'updatedAt' => now()->toIso8601String(),
        ];

        // Gunakan metode createAspiration dari FirebaseService
        // Pastikan metode createAspiration di FirebaseService menerima $aspirationId sebagai parameter
        // atau menangani pembuatan ID dokumen secara internal jika $aspirationId tidak dilewatkan.
        // Jika $aspirationId adalah field dalam data, maka FirebaseService harus tahu untuk tidak menggunakannya sebagai nama dokumen.
        // Biasanya, ID dokumen Firestore dibuat oleh Firestore atau disediakan saat pembuatan.
        // Untuk konsistensi, kita akan menggunakan $aspirationId sebagai nama dokumen.
        // Ubah createAspiration di service agar bisa menerima ID dokumen:
        // $this->firebase->createAspiration($aspirationId, $aspirationData);
        // Atau jika createAspiration hanya butuh data:
        $this->firebase->createAspiration($aspirationData); // Asumsi createAspiration akan menggunakan $data['id'] untuk ID dokumen jika perlu

        return response()->json(['message' => 'Aspiration submitted successfully', 'data' => $aspirationData], 201);
    }

    /**
     * Display a listing of the aspirations.
     * Guru BK melihat semua, Siswa melihat miliknya sendiri.
     */
    public function index(Request $request)
    {
        $authUser = $this->getAuthUser($request); // Pengecekan token manual
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $aspirationsData = [];

        if ($authUser->role === 'guru_bk') {
            $response = $this->firebase->getAllAspirations();
        } elseif ($authUser->role === 'siswa') {
            $response = $this->firebase->getAspirationsByUserId($authUser->sub);
        } else {
            return response()->json(['message' => 'You do not have permission to view aspirations.'], 403);
        }

        if (isset($response['documents'])) {
            foreach ($response['documents'] as $doc) {
                $fields = $doc['fields'];
                $aspirationsData[] = [
                    // ID dokumen diambil dari 'name' field path, lalu ambil bagian terakhirnya
                    'id' => basename($doc['name']),
                    'userId' => $fields['userId']['stringValue'] ?? null,
                    'username' => $fields['username']['stringValue'] ?? 'N/A',
                    'name' => $fields['name']['stringValue'] ?? ($fields['username']['stringValue'] ?? 'N/A'), // Tampilkan nama atau username
                    'content' => $fields['content']['stringValue'] ?? '',
                    'createdAt' => $fields['createdAt']['timestampValue'] ?? null,
                    'updatedAt' => $fields['updatedAt']['timestampValue'] ?? null,
                ];
            }
        } elseif (isset($response['error'])) {
            // Log error jika perlu: \Log::error('Firebase Error: ' . json_encode($response['error']));
            return response()->json(['message' => 'Failed to fetch aspirations', 'error_detail' => $response['error']['message'] ?? 'Unknown error'], 500);
        }
        
        if (!empty($aspirationsData) && isset($aspirationsData[0]['createdAt'])) {
            usort($aspirationsData, function ($a, $b) {
                // Handle null createdAt values if any
                $timeA = $a['createdAt'] ? strtotime($a['createdAt']) : 0;
                $timeB = $b['createdAt'] ? strtotime($b['createdAt']) : 0;
                return $timeB - $timeA; // Sort descending
            });
        }

        return response()->json($aspirationsData);
    }

    public function show(Request $request, $aspirationId)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Anda perlu metode di FirebaseService untuk mengambil satu aspirasi berdasarkan ID dokumen
        // Misalnya: $this->firebase->getAspirationById($aspirationId)
        // Metode ini harus mengembalikan struktur yang mirip dengan getAspirationsByUserId atau getAllAspirations tapi untuk satu dokumen.
        $aspirationDoc = $this->firebase->getAspirationsByUserId($aspirationId); // Asumsi metode ini ada di FirebaseService

        if (!$aspirationDoc || isset($aspirationDoc['error'])) {
            $errorMessage = isset($aspirationDoc['error']['message']) ? $aspirationDoc['error']['message'] : 'Aspiration not found or failed to fetch.';
            if (strpos(strtolower($errorMessage), 'not found') !== false || (isset($aspirationDoc['error']['code']) && $aspirationDoc['error']['code'] === 5)) { // Firestore error code 5 is NOT_FOUND
                 return response()->json(['message' => 'Aspiration not found.'], 404);
            }
            return response()->json(['message' => 'Failed to fetch aspiration', 'error_detail' => $errorMessage], 500);
        }
        
        if (!isset($aspirationDoc['fields'])) {
             return response()->json(['message' => 'Aspiration not found or data is malformed.'], 404);
        }

        $fields = $aspirationDoc['fields'];
        $aspirationData = [
            'id' => basename($aspirationDoc['name']), // Mengambil ID dari path 'name'
            'userId' => $fields['userId']['stringValue'] ?? null,
            'username' => $fields['username']['stringValue'] ?? 'N/A',
            'name' => $fields['name']['stringValue'] ?? ($fields['username']['stringValue'] ?? 'N/A'),
            'content' => $fields['content']['stringValue'] ?? '',
            'createdAt' => $fields['createdAt']['timestampValue'] ?? ($fields['createdAt']['stringValue'] ?? null),
            'updatedAt' => $fields['updatedAt']['timestampValue'] ?? ($fields['updatedAt']['stringValue'] ?? null),
            // 'status' => $fields['status']['stringValue'] ?? 'submitted',
        ];

        // Otorisasi: Guru BK bisa lihat semua, Siswa hanya miliknya
        if ($authUser->role === 'siswa' && $aspirationData['userId'] !== $authUser->sub) {
            return response()->json(['message' => 'You do not have permission to view this aspiration.'], 403);
        }

        if ($authUser->role !== 'guru_bk' && $authUser->role !== 'siswa') {
             return response()->json(['message' => 'You do not have permission to view aspirations.'], 403);
        }

        return response()->json($aspirationData);
    }

    /**
     * Remove the specified aspiration from storage.
     * Guru BK dapat menghapus semua, Siswa hanya dapat menghapus miliknya.
     */
    public function destroy(Request $request, $aspirationId)
    {
        $authUser = $this->getAuthUser($request);
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Pertama, ambil data aspirasi untuk verifikasi pemilik
        $aspirationDoc = $this->firebase->getAspirationsByUserId($aspirationId); // Asumsi metode ini ada

        if (!$aspirationDoc || isset($aspirationDoc['error']) || !isset($aspirationDoc['fields'])) {
            $errorMessage = 'Aspiration not found or failed to fetch for deletion.';
            if (isset($aspirationDoc['error']['message']) && strpos(strtolower($aspirationDoc['error']['message']), 'not found') !== false) {
                 $errorMessage = 'Aspiration not found.';
            } else if (isset($aspirationDoc['error']['code']) && $aspirationDoc['error']['code'] === 5) { // Firestore error code 5 is NOT_FOUND
                 $errorMessage = 'Aspiration not found.';
            }
            return response()->json(['message' => $errorMessage], ($errorMessage === 'Aspiration not found.') ? 404 : 500);
        }


        $aspirationUserId = $aspirationDoc['fields']['userId']['stringValue'] ?? null;

        // Otorisasi penghapusan
        if ($authUser->role === 'siswa' && $aspirationUserId !== $authUser->sub) {
            return response()->json(['message' => 'You do not have permission to delete this aspiration.'], 403);
        } elseif ($authUser->role !== 'guru_bk' && $authUser->role !== 'siswa') {
            return response()->json(['message' => 'You do not have permission to delete aspirations.'], 403);
        }
        
        // Jika user adalah 'siswa' dan bukan pemilik, atau user bukan 'guru_bk' atau 'siswa'.
        if (!($authUser->role === 'guru_bk' || ($authUser->role === 'siswa' && $aspirationUserId === $authUser->sub))) {
            return response()->json(['message' => 'Insufficient permissions to delete this aspiration.'], 403);
        }


        // Anda perlu metode di FirebaseService untuk menghapus aspirasi berdasarkan ID dokumen
        // Misalnya: $this->firebase->deleteAspiration($aspirationId)
        $deleteResponse = $this->firebase->deleteAspiration($aspirationId); // Asumsi metode ini ada di FirebaseService

        if (isset($deleteResponse['error'])) {
            // \Log::error('Firebase Delete Error: ' . json_encode($deleteResponse['error']));
            return response()->json(['message' => 'Failed to delete aspiration', 'error_detail' => $deleteResponse['error']['message'] ?? 'Unknown error'], 500);
        }
        
        // Firestore REST API for delete returns an empty JSON {} on success.
        // If $deleteResponse is empty (or doesn't have an 'error' key), assume success.
        return response()->json(['message' => 'Aspiration deleted successfully'], 200);
    }


    // Opsional: Method untuk Guru BK mengupdate status aspirasi
    // public function updateStatus(Request $request, $aspirationId)
    // {
    //     $authUser = $this->getAuthUser($request);
    //     if (!$authUser || $authUser->role !== 'guru_bk') {
    //         return response()->json(['message' => 'Unauthorized or insufficient permissions.'], 403);
    //     }

    //     $data = $request->validate([
    //         'status' => 'required|string|in:submitted,reviewed,in_progress,resolved,rejected',
    //     ]);

    //     // Anda perlu method update di FirebaseService untuk koleksi 'aspirations', misal:
    //     // $this->firebase->updateAspirationStatus($aspirationId, $data['status']);
        
    //     return response()->json(['message' => 'Aspiration status updated successfully']);
    // }
}