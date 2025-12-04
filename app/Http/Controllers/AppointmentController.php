<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;
use Carbon\Carbon; // Add this line
use App\Models\SalonSetting; // Add this line

class AppointmentController extends Controller
{
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

    // Keep the query string in pagination links (so filters don't disappear when paginating)
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

    // Get salon settings using your model's getSettings method
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
        $services = Service::where('is_active', true)->get();
        $staff = Staff::with('user')->get();

        return view('dashboard.appointments.create', compact('customers', 'services', 'staff'));
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
    
    // Validate business hours
    $startDateTime = new \DateTime($request->start_datetime);
    $startTime = $startDateTime->format('H:i:s');
    
    if ($startTime < $salonSettings->opening_time || $startTime > $salonSettings->closing_time) {
        return back()->withErrors([
            'start_datetime' => 'Appointments must be within business hours: ' . 
            \Carbon\Carbon::createFromFormat('H:i:s', $salonSettings->opening_time)->format('g:i A') . ' - ' . 
            \Carbon\Carbon::createFromFormat('H:i:s', $salonSettings->closing_time)->format('g:i A')
        ]);
    }

    // Validate max days ahead
    $maxDate = now()->addDays($salonSettings->max_days_book_ahead);
    if ($startDateTime > $maxDate) {
        return back()->withErrors([
            'start_datetime' => "Appointments can only be booked up to {$salonSettings->max_days_book_ahead} days in advance."
        ]);
    }

    // Check for double booking
    $exists = Appointment::where('staff_id', $request->staff_id)
        ->where('start_datetime', $request->start_datetime)
        ->exists();

    if ($exists) {
        return back()->withErrors(['start_datetime' => 'This time slot is already booked.']);
    }

    $service = Service::findOrFail($request->service_id);
    
    Appointment::create([
        'customer_id' => $request->customer_id,
        'service_id' => $request->service_id,
        'staff_id' => $request->staff_id,
        'start_datetime' => $request->start_datetime,
        'total_amount' => $service->price_premium ?? $service->price_regular,
        'status' => 'scheduled',
    ]);

    return redirect()->route('dashboard.appointments.index')
        ->with('success', 'Appointment created successfully!');
}
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled'
        ]);

        $appointment->update(['status' => $request->status]);

        return back()->with('success', 'Appointment status updated!');
    }
}