<?php

use App\Http\Controllers\Api\KasusSiswaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\AbsensiViewController;







Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.register'))->name('register');
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('forgot');
Route::get('/reset-password', fn() => view('auth.reset-password'))->name('reset-password');
Route::get('/create', fn() => view('kasus_siswa.index_guru'))->name('create');
Route::get('/menambah', fn() => view('kasus_siswa.create'))->name('kasus_siswa.create_view');
Route::get('/kasus-siswa/{id}/edit-page', function ($id) {
    return view('kasus_siswa.edit', ['caseId' => $id]);
})->name('kasus_siswa.edit_page');

Route::get('/showkasus/{id}/edit-page', function ($id) {
    return view('kasus_siswa.show', ['caseId' => $id]);
})->name('kasus_siswa.show_page');

Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');





//Route::resource('siswa', SiswaController::class);
Route::get('/siswa', fn() => view('siswa.edit'))->name('index');
Route::get('/tempat', fn() => view('siswa.tempat'))->name('tempat');
// Jika Anda ingin rute yang lebih spesifik atau kustom, Anda bisa menuliskannya secara manual:
// Route::get('/users', [UserController::class, 'index'])->name('users.index');
// Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
// Route::post('/users', [UserController::class, 'store'])->name('users.store');
// Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
// Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
// Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
// Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('/siswa', [SiswaController::class, 'index'])->name('api.siswa.index'); // For listing
Route::post('/siswa', [SiswaController::class, 'store'])->name('api.siswa.store');
Route::get('/siswa/{id}', [SiswaController::class, 'show'])->name('api.siswa.show'); // For showing one
Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit'])->name('api.siswa.edit'); // Route to show edit form
Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('api.siswa.update'); // Route to handle update
Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('api.siswa.destroy');

Route::get('/riwayat-kasus-saya', [KasusSiswaController::class, 'showSiswaRiwayatKasus'])
         ->name('kasus_siswa.index_siswa');


Route::get('/absensi-saya', [AbsensiViewController::class, 'showMyAttendancePage'])->name('absensi.page.mahasiswa');
Route::get('/laporan-harian-absensi', [AbsensiViewController::class, 'showGuruDailyReportPage'])->name('absensi.page.guru_harian');
Route::get('/laporan-bulanan-absensi', [AbsensiViewController::class, 'showGuruMonthlyReportPage'])->name('absensi.page.guru_bulanan');