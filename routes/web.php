<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StaffController;        // ← ADD THIS
use App\Http\Controllers\MessageController;     // ← ADD THIS (for messaging)
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

    // Home redirect based on role
    Route::get('/home', function () {
        if (auth()->user()->isAdmin() || auth()->user()->isStaff()) {
            return redirect()->route('dashboard.index');
        }
        return view('landing');
    })->name('home');

    // Messaging routes (accessible by all authenticated users)
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/{user}', [MessageController::class, 'getConversation'])->name('conversation');
        Route::post('/{user}/send', [MessageController::class, 'sendMessage'])->name('send');
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unreadCount');
        Route::post('/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('markAllRead');
        Route::get('/notifications', [MessageController::class, 'getNotifications'])->name('notifications');
        Route::get('/refresh/{user}', [MessageController::class, 'refreshConversation'])->name('refresh');
    });

    // Staff-specific routes
    Route::middleware(['role:staff'])->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
        Route::get('/appointments', [StaffController::class, 'appointments'])->name('appointments');
        Route::get('/appointments/{appointment}', [StaffController::class, 'showAppointment'])->name('appointments.show');
        Route::get('/commission', [StaffController::class, 'commissionReport'])->name('commission');
        Route::post('/service-report', [StaffController::class, 'submitServiceReport'])->name('service-report');
        Route::get('/statistics', [StaffController::class, 'getStatistics'])->name('statistics');
    });

    // Admin-only routes (user management)
    Route::middleware(['role:admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role');
        Route::patch('/users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
    });

    // Routes accessible by both admin and staff
    Route::middleware(['role:admin,staff'])->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');

        // Appointments
        Route::resource('appointments', AppointmentController::class)->except(['destroy']);
        Route::post('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
        Route::get('appointments/{appointment}/cancel', [AppointmentController::class, 'showCancelForm'])->name('appointments.cancel.form');
        Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

        // Services
        Route::resource('services', ServiceController::class)->only(['index', 'show']);

        // Payments
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('appointments/{appointment}/payment/create', [PaymentController::class, 'createForAppointment'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::post('payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');
        Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::get('/appointments', [ReportsController::class, 'appointments'])->name('appointments');
            Route::get('/revenue', [ReportsController::class, 'revenue'])->name('revenue');
            Route::get('/inventory', [ReportsController::class, 'inventory'])->name('inventory');
            Route::post('/download', [ReportsController::class, 'download'])->name('download');
        });

        // Inventory
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/daily-update', [InventoryController::class, 'dailyUpdate'])->name('inventory.daily-update');
        Route::post('/inventory/daily-update-save', [InventoryController::class, 'saveDailyUpdates'])->name('inventory.daily-update.save');
        Route::get('/inventory/items/{item}', [InventoryController::class, 'getItem'])->name('inventory.items.show');
        Route::post('/inventory/items', [InventoryController::class, 'storeItem'])->name('inventory.items.store');
        Route::post('/inventory/items/{item}/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.items.update-stock');
    });

    // AJAX route for dashboard filtering (accessible to admin & staff)
    Route::post('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');
});

// Legacy routes (kept for backward compatibility or external links)
Route::get('/appointments', [AppointmentController::class, 'index'])->name('dashboard.appointments.index');
Route::get('/staff', [StaffController::class, 'index'])->name('dashboard.staff.index');
Route::get('/reports/financial', [ReportsController::class, 'financial'])->name('dashboard.reports.financial');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('dashboard.appointments.store');
Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('dashboard.appointments.create');