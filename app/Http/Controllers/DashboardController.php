<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Staff;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if user has access to dashboard
        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            abort(403, 'Unauthorized access to dashboard.');
        }

        // Get dashboard statistics
        $stats = [
            'today_appointments' => Appointment::whereDate('start_datetime', today())->count(),
            'total_customers' => Customer::count(),
            'total_staff' => User::where('role', 'staff')->count(),
            'revenue_today' => Appointment::whereDate('start_datetime', today())
                ->where('status', 'completed')
                ->sum('total_amount'),
        ];

        // Get today's appointments
        $todayAppointments = Appointment::with(['customer', 'service', 'staff.user'])
            ->whereDate('start_datetime', today())
            ->orderBy('start_datetime')
            ->get();

        // Get upcoming appointments
        $upcomingAppointments = Appointment::with(['customer', 'service', 'staff.user'])
            ->where('start_datetime', '>', now())
            ->where('start_datetime', '<=', now()->addDays(7))
            ->orderBy('start_datetime')
            ->limit(10)
            ->get();

        // Get data for modals
        $customers = Customer::all();
        $services = Service::where('is_active', true)->get();
        $staff = Staff::with('user')->get();

        return view('dashboard.index', compact('stats', 'todayAppointments', 'upcomingAppointments', 'customers', 'services', 'staff'));
    }
}