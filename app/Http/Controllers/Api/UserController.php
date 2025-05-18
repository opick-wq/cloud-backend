<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Google\Cloud\Firestore\FirestoreClient;


class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request, FirestoreClient $firestore)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
        ]);

        // Simpan ke Firestore
        $docRef = $firestore->collection('users')->add([
            'name' => $request->name,
            'email' => $request->email,
            'created_at' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'message' => 'User saved to Firebase Firestore!',
            'id' => $docRef->id(),
        ], 201);
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(null, 204);
    }
}
