<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache; // simpan OTP sementara
use App\Mail\VerifikasiOtpMail;


class UserController extends Controller
{
    protected $firebase;
    protected $jwtSecret;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
        $this->jwtSecret = env('JWT_SECRET'); // set di .env
    }

    public function register(Request $request)
    {
    $data = $request->validate([
        'username' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:8',
    ]);

    if ($this->firebase->findByEmail($data['email'])) {
        return response()->json(['message' => 'Email already registered'], 400);
    }

    $userId = (string) Str::uuid();

    $initial = strtoupper(substr($data['name'], 0, 2));
    $avatarUrl = "https://ui-avatars.com/api/?name={$initial}&background=random";

    $this->firebase->create([
        'id' => $userId,
        'username' => $data['username'],
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
        'role' => 'siswa',
        'avatarUrl' => $avatarUrl,
    ]);

    return response()->json(['message' => 'Registration successful'], 201);
    }




    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string', 
            'password' => 'required|string'
        ]);

        // Ganti ke findByUsername (harus Anda buat di FirebaseService)
        $userData = $this->firebase->findByUsername($credentials['username']); 

        if (!$userData) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $stored = $userData['fields'];

        if (!Hash::check($credentials['password'], $stored['password']['stringValue'])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $payload = [
            'sub' => $userData['id'],
            'email' => $stored['email']['stringValue'],
            'username' => $stored['username']['stringValue'],
            'role' => $stored['role']['stringValue'],
            'iat' => time(),
            'exp' => time() + 60 * 60
        ];

        $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

        return response()->json([
            'message' => 'Login successful',
            'token' => $jwt,
            'user' => [
                'id' => $userData['id'],
                'username' => $stored['username']['stringValue'],
                'email' => $stored['email']['stringValue'],
                'role' => $stored['role']['stringValue'],
            ]
        ]);
    }

    public function adminLogin(Request $request)
    {
    $credentials = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string'
    ]);

    $userData = $this->firebase->findByUsername($credentials['username']); 

    if (!$userData) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $stored = $userData['fields'];

    if (!Hash::check($credentials['password'], $stored['password']['stringValue'])) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // ❗ Cek role di sini
    $role = $stored['role']['stringValue'] ?? 'user';
    if ($role !== 'admin') {
        return response()->json(['message' => 'Access denied. Only admins can login here.'], 403);
    }

    // ✅ Jika admin, buat token
    $payload = [
        'sub' => $userData['id'],
        'email' => $stored['email']['stringValue'],
        'username' => $stored['username']['stringValue'],
        'role' => $role,
        'iat' => time(),
        'exp' => time() + 60 * 60 // 1 jam
    ];

    $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

    return response()->json([
        'message' => 'Admin login successful',
        'token' => $jwt,
        'user' => [
            'id' => $userData['id'],
            'username' => $stored['username']['stringValue'],
            'email' => $stored['email']['stringValue'],
            'role' => $role,
        ]
    ]);
    }



    public function logout(Request $request)
    {
        // Untuk JWT, logout cukup dilakukan di client (hapus token)
        return response()->json(['message' => 'Logged out (client-side only)']);
    }

    public function me(Request $request)
    {
    $authHeader = $request->header('Authorization');

    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        return response()->json(['message' => 'Token missing'], 401);
    }

    $token = str_replace('Bearer ', '', $authHeader);

    try {
        $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));

        // Ambil data user dari Firestore berdasarkan ID
        $userData = $this->firebase->findByUsername($decoded->username); // atau bisa juga pakai ->findById($decoded->sub)
        $stored = $userData['fields'];

        return response()->json([
            'user' => [
                'id' => $decoded->sub,
                'email' => $decoded->email,
                'username' => $decoded->username,
                'role' => $decoded->role,
                'avatarUrl' => $stored['avatarUrl']['stringValue'] ?? null,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Invalid token'], 401);
    }
    }




    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Cek user berdasarkan email
        $user = $this->firebase->findByEmail($request->email);

        if (!$user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        $email = $request->email;

        // Simpan OTP ke Cache untuk 10 menit
        Cache::put("otp_{$email}", $otp, now()->addMinutes(10));

        // Kirim email OTP
        Mail::to($email)->send(new VerifikasiOtpMail($otp));

        return response()->json(['message' => 'Verification code sent to email']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        $cachedOtp = Cache::get("otp_{$request->email}");

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $user = $this->firebase->findByEmail($request->email);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update password di Firestore
        $this->firebase->update($user['id'], [
            'password' => bcrypt($request->new_password)
        ]);

        // Hapus OTP dari cache
        Cache::forget("otp_{$request->email}");

        return response()->json(['message' => 'Password has been reset successfully']);
    }

    public function index()
    {
        $response = $this->firebase->getAll();

        $users = [];
        if (isset($response['documents'])) {
            foreach ($response['documents'] as $doc) {
                $fields = $doc['fields'];
                $users[] = [
                    'id' => basename($doc['name']),
                    'name' => $fields['name']['stringValue'] ?? '',
                    'email' => $fields['email']['stringValue'] ?? '',
                    'username' => $fields['username']['stringValue'] ?? '',
                    'role' => $fields['role']['stringValue'] ?? '',
                    'password' => $fields['password']['stringValue'] ?? null, 
                ];
            }
        }

        return response()->json($users);
    }

    // POST /users
    public function store(Request $request)
    {
        $data = $request->only(['name', 'email', 'username', 'role','password']);

        $this->firebase->create($data);

        return response()->json(['message' => 'User created successfully']);
    }

    // GET /users/{id}
    public function show($id)
    {
        $doc = $this->firebase->get($id);

        if (!isset($doc['fields'])) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $fields = $doc['fields'];

        return response()->json([
            'id' => $id,
            'name' => $fields['name']['stringValue'] ?? '',
            'email' => $fields['email']['stringValue'] ?? '',
            'username' => $fields['username']['stringValue'] ?? '',
            'role' => $fields['role']['stringValue'] ?? '',
            'password' => $fields['password']['stringValue'] ?? '',
        ]);
    }

    // PUT /users/{id}
   public function update(Request $request, $id)
    {
    $data = $request->only(['name', 'password', 'email', 'username', 'role']);

    // Enkripsi password jika disertakan dalam permintaan
    if (!empty($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    } else {
        // Hapus field password jika kosong agar tidak menimpa yang lama dengan nilai kosong
        unset($data['password']);
    }

    $this->firebase->update($id, $data);

    return response()->json(['message' => 'User updated successfully']);
    }



    // DELETE /users/{id}
    public function destroy($id)
    {
        $this->firebase->delete($id);

        return response()->json(['message' => 'User deleted successfully']);
    }

     public function registerAdmin(Request $request)
    {
    $data = $request->validate([
        'username' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:8',
        'role' => 'required|string|in:admin,siswa,guru_bk',
    ]);

    if ($this->firebase->findByEmail($data['email'])) {
        return response()->json(['message' => 'Email already registered'], 400);
    }

    $userId = (string) Str::uuid();

    $initial = strtoupper(substr($data['name'], 0, 2));
    $avatarUrl = "https://ui-avatars.com/api/?name={$initial}&background=random";

    $this->firebase->create([
        'id' => $userId,
        'username' => $data['username'],
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
        'role' => $data['role'],
        'avatarUrl' => $avatarUrl,
    ]);

    return response()->json(['message' => 'Registration successful'], 201);
    }

}
