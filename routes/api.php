<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\DetailSiswaController;
use App\Http\Controllers\Api\AspirationController;
use App\Http\Controllers\Api\KasusSiswaController;
use App\Http\Controllers\Api\AbsensiController;


//admin routes
Route::post('/admin/register', [UserController::class, 'registerAdmin']);
Route::post('/admin/login', [UserController::class, 'login']);

//aspirasi buat murid post
Route::post('/aspirations', [AspirationController::class, 'store']);
//aspirasi buat table guru_bk
Route::get('/aspirations', [AspirationController::class, 'index']);
Route::get('/aspirations/{id}', [AspirationController::class, 'show']);
Route::delete('/aspirations/{id}', [AspirationController::class, 'destroy']);


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

    //halaman untuk guru bk untuk melihat profil seluruh siswa
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
//untuk update siswa
Route::put('/profile', [SiswaController::class, 'showProfile']);

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



 //jurnal student halaman guru_bk
Route::post('/student-cases', [KasusSiswaController::class, 'store']);
Route::get('/student-cases/{id}', [KasusSiswaController::class, 'show']);
Route::put('/student-cases/{id}', [KasusSiswaController::class, 'update']);
Route::delete('/student-cases/{id}', [KasusSiswaController::class, 'destroy']);

//ini bisa digunakan buat guru dan halaman jurnal siswa karena deteksi berdasarkan rolenya
Route::get('/student-cases', [KasusSiswaController::class, 'index']);

//rute untuk absen siswa
Route::post('/attendance/submit', [AbsensiController::class, 'submitStudentAttendance']);
Route::get('/attendance/today', [AbsensiController::class, 'getMyTodaysAttendance']);


    // Rute lihat absen untuk Guru BK
Route::get('/attendance/report/daily', [AbsensiController::class, 'getDailyReport']); // Asumsi ada middleware role API
Route::get('/attendance/report/monthly', [AbsensiController::class, 'getMonthlyReport']);
Route::put('/absensi/update/{attendanceId}', [AbsensiController::class, 'updateAttendance'])->name('absensi.update'); // Gunakan ID absensi dari Firebase
Route::post('/absensi/manual-add', [AbsensiController::class, 'manualAddAttendance'])->name('absensi.manual_add');