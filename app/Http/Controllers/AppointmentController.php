<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['customer', 'service', 'staff.user'])
            ->orderBy('start_datetime', 'desc')
            ->paginate(20);

        return view('dashboard.appointments.index', compact('appointments'));
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