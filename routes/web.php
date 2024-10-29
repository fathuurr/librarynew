<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::resource('books', BookController::class)
        ->middleware(['role:super_admin|petugas_perpus']);

    Route::resource('borrowings', BorrowingController::class)
        ->middleware(['role:super_admin|petugas_perpus|siswa']);

    Route::post('borrowings/{borrowing}/return', [BorrowingController::class, 'return'])
        ->name('borrowings.return')
        ->middleware(['role:super_admin|petugas_perpus']);

    Route::resource('users', UserController::class)
        ->middleware(['role:super_admin']);
});

Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/balance', [App\Http\Controllers\StudentController::class, 'showBalance'])
        ->name('balance');
});

