<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\SalonSetting;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Helper method to check if a staff member is available for a given time slot.
     */
    private function isStaffAvailable($staffId, $startDateTime, $durationMinutes, $excludeAppointmentId = null)
    {
        $start = Carbon::parse($startDateTime);
        $end = $start->copy()->addMinutes($durationMinutes);

        // Check for overlapping appointments
        $query = Appointment::where('staff_id', $staffId)
            ->where(function ($q) use ($start, $end) {
                // New appointment starts during an existing one
                $q->whereBetween('start_datetime', [$start, $end->copy()->subMinute()])
                  // Existing appointment starts during the new one
                  ->orWhereBetween('end_datetime', [$start->copy()->addMinute(), $end])
                  // New appointment completely encompasses an existing one
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_datetime', '<=', $start)
                         ->where('end_datetime', '>=', $end);
                  });
            })
            ->whereNotIn('status', ['cancelled', 'no_show']); // Ignore cancelled/no-show

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return !$query->exists();
    }

    public function index()
    {
        // Start with the base query
        $query = Appointment::with(['customer', 'service', 'staff.user', 'payment']);

        // 1. Search filter (customer name, phone, or service name)
        if ($search = request('search')) {
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

        // 2. Status filter
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // 3. Date filter (only the date part of start_datetime)
        if ($date = request('date')) {
            $query->whereDate('start_datetime', $date);
        }

        // Default sorting: newest first
        $appointments = $query->orderBy('start_datetime', 'desc')->paginate(20);

        // Keep the query string in pagination links
        $appointments->appends(request()->query());

        // Calculate statistics
        $totalAppointments = Appointment::count();
        $todayAppointments = Appointment::whereDate('start_datetime', today())->count();

        // Completed appointments this month
        $completedThisMonth = Appointment::where('status', 'completed')
            ->whereMonth('start_datetime', now()->month)
            ->whereYear('start_datetime', now()->year)
            ->count();

        // Get data for modal
        $customers = Customer::all();
        $services = Service::where('is_active', true)->get();
        $staff = Staff::with('user')->get();

        // Get salon settings
        $salonSettings = SalonSetting::getSettings();

        // Use salon settings or defaults
        $openingTime = $salonSettings->opening_time;
        $closingTime = $salonSettings->closing_time;
        $maxDaysAhead = $salonSettings->max_days_book_ahead;
        $slotInterval = $salonSettings->slot_interval_minutes;

        return view('dashboard.appointments.index', compact(
            'appointments',
            'totalAppointments',
            'todayAppointments',
            'completedThisMonth',
            'customers',
            'services',
            'staff',
            'openingTime',
            'closingTime',
            'maxDaysAhead',
            'slotInterval'
        ));
    }

    public function create()
{
    $customers = Customer::all();
    
    // Get all staff
    $staff = Staff::with('user')->get();
    
    // Initially show all active services
    $services = Service::where('is_active', true)->with('category')->get();
    
    // Group services by category for better display
    $servicesByCategory = $services->groupBy('category.name');

    return view('dashboard.appointments.create', compact(
        'customers', 
        'services', 
        'staff',
        'servicesByCategory'
    ));
}
public function getServicesByStaff($staffId)
{
    $staff = Staff::findOrFail($staffId);
    
    $services = Service::whereHas('category', function($query) use ($staff) {
        $query->where('specialty', $staff->specialty)
              ->orWhere('specialty', 'both');
    })
    ->where('is_active', true)
    ->with('category')
    ->get();
    
    return response()->json([
        'services' => $services,
        'grouped_services' => $services->groupBy('category.name')
    ]);
}
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'start_datetime' => 'required|date|after_or_equal:today',
        ]);

        // Get salon settings
        $salonSettings = SalonSetting::getSettings();

        // Parse start datetime
        $startDateTime = Carbon::parse($request->start_datetime);
        $startTime = $startDateTime->format('H:i:s');

        // Validate business hours
        if ($startTime < $salonSettings->opening_time || $startTime > $salonSettings->closing_time) {
            return back()->withErrors([
                'start_datetime' => 'Appointments must be within business hours: ' .
                    Carbon::createFromFormat('H:i:s', $salonSettings->opening_time)->format('g:i A') . ' - ' .
                    Carbon::createFromFormat('H:i:s', $salonSettings->closing_time)->format('g:i A')
            ]);
        }

        // Validate max days ahead
        $maxDate = now()->addDays($salonSettings->max_days_book_ahead);
        if ($startDateTime->gt($maxDate)) {
            return back()->withErrors([
                'start_datetime' => "Appointments can only be booked up to {$salonSettings->max_days_book_ahead} days in advance."
            ]);
        }

        // Get service duration
        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration_minutes;

        // Minimum duration check
        if ($duration < 30) {
            return back()->withErrors([
                'service_id' => 'Service duration must be at least 30 minutes.'
            ]);
        }

        // Check staff availability (proper overlap detection)
        if (!$this->isStaffAvailable($request->staff_id, $request->start_datetime, $duration)) {
            return back()->withErrors([
                'start_datetime' => 'Staff is not available for the selected time slot. Please choose another time.'
            ]);
        }

        // Calculate end time
        $endDateTime = $startDateTime->copy()->addMinutes($duration);

        // Ensure appointment doesn't exceed closing time
        $closingTimeToday = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $salonSettings->closing_time);
        if ($endDateTime->gt($closingTimeToday)) {
            return back()->withErrors([
                'start_datetime' => 'Appointment would end after closing time. Please select an earlier time.'
            ]);
        }

           Appointment::create([
        'customer_id' => $request->customer_id,
        'service_id' => $request->service_id,
        'staff_id' => $request->staff_id,
        'start_datetime' => $request->start_datetime,
        'end_datetime' => $endDateTime,
        'total_amount' => $service->price_premium ?? $service->price_regular,
        'status' => 'scheduled',
    ]);

    ToastHelper::success('Appointment created successfully!');
    return redirect()->route('dashboard.appointments.index');

        return redirect()->route('dashboard.appointments.index')
            ->with('success', 'Appointment created successfully!');
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'start_datetime' => 'required|date',
        ]);

        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration_minutes;

        // Check staff availability, excluding the current appointment itself
        if (!$this->isStaffAvailable($request->staff_id, $request->start_datetime, $duration, $appointment->id)) {
            return back()->withErrors([
                'start_datetime' => 'Staff is not available for the selected time slot.'
            ]);
        }

        $startDateTime = Carbon::parse($request->start_datetime);
        $endDateTime = $startDateTime->copy()->addMinutes($duration);

        $appointment->update([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $endDateTime,
            'total_amount' => $service->price_premium ?? $service->price_regular,
        ]);

        return redirect()->route('dashboard.appointments.index')
            ->with('success', 'Appointment updated successfully!');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show'
        ]);

        $appointment->update(['status' => $request->status]);

        return back()->with('success', 'Appointment status updated!');
    }
    // Add these methods to AppointmentController.php
public function showCancelForm(Appointment $appointment)
{
    return view('dashboard.appointments.cancel', compact('appointment'));
}

public function cancel(Request $request, Appointment $appointment)
{
    $request->validate([
        'cancellation_reason' => 'required|string|max:500',
        'status' => 'required|in:cancelled,failed',
        'refund_amount' => 'nullable|numeric|min:0',
        'refund_method' => 'nullable|in:cash,gcash,bank_transfer'
    ]);

    // Use the helper method from the model
    if ($request->status === 'cancelled') {
        $appointment->cancel($request->cancellation_reason, auth()->user());
    } else {
        $appointment->markAsFailed($request->cancellation_reason);
    }

    // Handle refund if applicable
    if ($request->refund_amount > 0 && $appointment->payment) {
        // Create refund record
        Payment::create([
            'appointment_id' => $appointment->id,
            'customer_id' => $appointment->customer_id,
            'amount' => -$request->refund_amount,
            'method' => $request->refund_method,
            'status' => 'refunded',
            'notes' => "Refund for {$request->status} appointment: " . $request->cancellation_reason,
            'paid_at' => now()
        ]);
    }

    return redirect()->route('dashboard.appointments.index')
        ->with('success', "Appointment has been {$request->status} successfully.");
}
}