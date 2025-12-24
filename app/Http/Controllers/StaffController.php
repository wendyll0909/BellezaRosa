<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Staff;
use App\Models\Commission;
use Carbon\Carbon;

class StaffController extends Controller
{
    /**
     * Show staff dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $staff = $user->staff;
        
        if (!$staff) {
            abort(403, 'Staff profile not found.');
        }

        // Get today's appointments for this staff
        $todaySchedule = Appointment::where('staff_id', $staff->id)
            ->whereDate('start_datetime', today())
            ->with(['customer', 'service'])
            ->orderBy('start_datetime')
            ->get()
            ->map(function($appointment) {
                $appointment->duration = $appointment->service->duration_minutes;
                return $appointment;
            });

        // Get upcoming appointments (next 7 days)
        $upcomingAppointments = Appointment::where('staff_id', $staff->id)
            ->where('start_datetime', '>', now())
            ->where('start_datetime', '<=', now()->addDays(7))
            ->with(['customer', 'service'])
            ->orderBy('start_datetime')
            ->limit(10)
            ->get();

        // Statistics
        $todayAppointments = Appointment::where('staff_id', $staff->id)
            ->whereDate('start_datetime', today())
            ->count();

        $upcomingAppointmentsCount = Appointment::where('staff_id', $staff->id)
            ->where('start_datetime', '>', now())
            ->where('start_datetime', '<=', now()->addDays(7))
            ->count();

        $completedThisMonth = Appointment::where('staff_id', $staff->id)
            ->where('status', 'completed')
            ->whereMonth('start_datetime', now()->month)
            ->whereYear('start_datetime', now()->year)
            ->count();

        // Monthly commission (we'll implement commission system next)
        $monthlyCommission = Commission::where('staff_id', $staff->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return view('dashboard.staff', compact(
            'todaySchedule',
            'upcomingAppointments',
            'todayAppointments',
            'upcomingAppointmentsCount',
            'completedThisMonth',
            'monthlyCommission'
        ));
    }

    /**
     * Get staff-specific appointments
     */
    public function appointments(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;
        
        if (!$staff) {
            abort(403, 'Staff profile not found.');
        }

        $query = Appointment::where('staff_id', $staff->id)
            ->with(['customer', 'service', 'payment']);

        // Apply filters
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($cq) use ($search) {
                    $cq->where('full_name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('service', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($date = $request->get('date')) {
            $query->whereDate('start_datetime', $date);
        }

        $appointments = $query->orderBy('start_datetime', 'desc')->paginate(20);

        return view('dashboard.staff.appointments', compact('appointments'));
    }

    /**
     * Show staff commission report
     */
    public function commissionReport(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;
        
        if (!$staff) {
            abort(403, 'Staff profile not found.');
        }

        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $commissions = Commission::where('staff_id', $staff->id)
            ->when($year, function($query) use ($year) {
                return $query->whereYear('created_at', $year);
            })
            ->when($month, function($query) use ($month) {
                return $query->whereMonth('created_at', $month);
            })
            ->with(['appointment.service', 'appointment.customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Summary statistics
        $monthlySummary = Commission::where('staff_id', $staff->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('SUM(amount) as total_commission, COUNT(*) as total_services')
            ->first();

        $yearlySummary = Commission::where('staff_id', $staff->id)
            ->whereYear('created_at', $year)
            ->selectRaw('SUM(amount) as yearly_total, COUNT(*) as yearly_services')
            ->first();

        return view('dashboard.staff.commission', compact(
            'commissions',
            'monthlySummary',
            'yearlySummary',
            'year',
            'month'
        ));
    }

    /**
     * Submit daily service report
     */
    public function submitServiceReport(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;
        
        if (!$staff) {
            abort(403, 'Staff profile not found.');
        }

        $request->validate([
            'report_date' => 'required|date',
            'completed_services' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
            'materials_used' => 'nullable|string|max:500'
        ]);

        // Update completed services
        if ($request->completed_services) {
            Appointment::whereIn('id', $request->completed_services)
                ->where('staff_id', $staff->id)
                ->update(['status' => 'completed']);
        }

        // Create service report
        ServiceReport::create([
            'staff_id' => $staff->id,
            'report_date' => $request->report_date,
            'completed_services' => $request->completed_services ?? [],
            'notes' => $request->notes,
            'materials_used' => $request->materials_used,
            'submitted_at' => now()
        ]);

        return redirect()->route('staff.dashboard')
            ->with('success', 'Service report submitted successfully!');
    }

    /**
     * Get staff statistics for AJAX updates
     */
    public function getStatistics()
    {
        $user = Auth::user();
        $staff = $user->staff;
        
        if (!$staff) {
            return response()->json(['success' => false]);
        }

        $todayAppointments = Appointment::where('staff_id', $staff->id)
            ->whereDate('start_datetime', today())
            ->count();

        $upcomingAppointments = Appointment::where('staff_id', $staff->id)
            ->where('start_datetime', '>', now())
            ->where('start_datetime', '<=', now()->addDays(7))
            ->count();

        $completedThisMonth = Appointment::where('staff_id', $staff->id)
            ->where('status', 'completed')
            ->whereMonth('start_datetime', now()->month)
            ->whereYear('start_datetime', now()->year)
            ->count();

        $monthlyCommission = Commission::where('staff_id', $staff->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return response()->json([
            'success' => true,
            'today_appointments' => $todayAppointments,
            'upcoming_appointments' => $upcomingAppointments,
            'completed_this_month' => $completedThisMonth,
            'monthly_commission' => $monthlyCommission
        ]);
    }

    /**
     * View appointment details (staff-specific)
     */
    public function showAppointment(Appointment $appointment)
    {
        $user = Auth::user();
        $staff = $user->staff;
        
        // Check if appointment belongs to this staff
        if ($appointment->staff_id !== $staff->id) {
            abort(403, 'You can only view your own appointments.');
        }

        $appointment->load(['customer', 'service', 'payment', 'addons']);

        return view('dashboard.staff.appointment-show', compact('appointment'));
    }
}