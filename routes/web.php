<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Guest can book without login
Route::post('/guest-book', [CustomerBookingController::class, 'store'])->name('guest.book');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // All logged-in users (admin/staff/customer) see the landing with notification bell
    Route::get('/home', function () {
        return view('landing');
    })->name('home');

    // ADMIN & STAFF ONLY – Main Dashboard
    Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');

        // Appointments - only for admin/staff
        Route::middleware(['auth'])->resource('appointments', AppointmentController::class);
        Route::post('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
            ->name('appointments.status');

        // Services & Categories
        Route::resource('services', ServiceController::class);

        // User / Account Management (Admin only)
        Route::middleware(['auth'])->get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role');
        Route::patch('users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
    });
});