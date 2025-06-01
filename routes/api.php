<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\DetailSiswaController;
use App\Http\Controllers\Api\AspirationController;


//admin routes
Route::post('/admin/register', [UserController::class, 'registerAdmin']);
Route::post('/admin/login', [UserController::class, 'adminLogin']);

//aspirasi buat murid post
Route::post('/aspirations', [AspirationController::class, 'store']);
//aspirasi buat table guru_bk
Route::get('/aspirations', [AspirationController::class, 'index']);


//users  routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
Route::get('/me', [UserController::class, 'me']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);




Route::prefix('siswa')->group(function () {

    //untuk melihat profil seluruh siswa
    Route::get('/', [SiswaController::class, 'index']);

    //menambahkan profil siswa 
    Route::post('/', [SiswaController::class, 'store']);

    //untuk melihat profil siswa berdasarkan id
    Route::get('/{id}', [SiswaController::class, 'show']);

    //update profil siswa berdasarkan id
    Route::put('/{id}', [SiswaController::class, 'update']);

    //delete profil siswa berdasarkan id
    Route::delete('/{id}', [SiswaController::class, 'destroy']);
});

//untuk siswa melihat profil siswa 
Route::get('/profile', [SiswaController::class, 'showProfile']);

Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit'])->name('api.siswa.edit');
    

Route::prefix('detail-siswa')->group(function () {
    Route::get('/', [DetailSiswaController::class, 'index']);
    Route::post('/', [DetailSiswaController::class, 'store']);
    Route::get('/{id}', [DetailSiswaController::class, 'show']);
    Route::put('/{id}', [DetailSiswaController::class, 'update']);
    Route::delete('/{id}', [DetailSiswaController::class, 'destroy']);
});


//tampilan semua guru bk untu chat guru bk di halaman siswa
 Route::get('/chatguru', [SiswaController::class, 'chatguru']);

 //aspirasi
