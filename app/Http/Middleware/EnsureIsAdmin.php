<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next)
{
    $jwtSecret = env('JWT_SECRET'); // ambil di sini, bukan di constructor

    $authHeader = $request->header('Authorization');
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        return response()->json(['message' => 'Token missing'], 401);
    }

    $token = str_replace('Bearer ', '', $authHeader);

    try {
        $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

        if ($decoded->role !== 'admin') {
            return response()->json(['message' => 'Forbidden: Only admin can access this'], 403);
        }

        $request->merge(['auth_user' => $decoded]);

        return $next($request);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Invalid token'], 401);
    }
}

}
