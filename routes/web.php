<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PaymentController;        // ← ONLY THIS LINE ADDED
use App\Http\Controllers\InventoryController; // ← ADD THIS LINE
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Custom Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Guest can book without login
Route::post('/guest-book', [CustomerBookingController::class, 'store'])->name('guest.book');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // All logged-in users see the landing with notification bell
    Route::get('/home', function () {
        return view('landing');
    })->name('home');

    // Dashboard routes with simple auth check
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        
        // Appointments
        Route::resource('appointments', AppointmentController::class);
        Route::post('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
            ->name('appointments.status');

        // Services
        Route::resource('services', ServiceController::class);
        
         // Simple Inventory Routes
Route::get('inventory/items/{item}', [InventoryController::class, 'getItem'])->name('inventory.items.show');
Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventory/daily-update', [InventoryController::class, 'dailyUpdate'])->name('inventory.daily-update');
    Route::post('inventory/items', [InventoryController::class, 'storeItem'])->name('inventory.items.store');
    Route::post('inventory/items/{item}/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.items.update-stock');
Route::post('inventory/daily-update-save', [InventoryController::class, 'saveDailyUpdates'])
    ->name('inventory.daily-update.save');
        // Payments
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('appointments/{appointment}/payment/create', [PaymentController::class, 'createForAppointment'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::post('payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');
        Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update'); 
    
        // User Management (Admin only) - using controller-level checks
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role');
        Route::patch('users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
    });
    
    // AJAX route for dashboard filtering
    Route::post('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');
});

Route::get('/appointments', [AppointmentController::class, 'index'])->name('dashboard.appointments.index');
Route::get('/staff', [StaffController::class, 'index'])->name('dashboard.staff.index');
Route::get('/reports/financial', [ReportController::class, 'financial'])->name('dashboard.reports.financial');
// Make sure you have these routes defined
Route::post('/appointments', [AppointmentController::class, 'store'])->name('dashboard.appointments.store');
Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('dashboard.appointments.create');