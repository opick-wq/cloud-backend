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