<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerBookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'email'          => 'nullable|email',
            'service_id'     => 'required|exists:services,id',
            'staff_id'       => 'required|exists:staff,id',
            'appointment_date'=> 'required|date|after_or_equal:today',
            'appointment_time'=> 'required',
        ]);

        $start = $request->appointment_date . ' ' . $request->appointment_time . ':00';

        // Prevent double booking
        $exists = Appointment::where('staff_id', $request->staff_id)
            ->where('start_datetime', $start)
            ->exists();

        if ($exists) {
            return back()->withErrors(['appointment_time' => 'This slot is already taken.']);
        }

        DB::transaction(function () use ($request, $start) {
            $customer = Customer::firstOrCreate(
                ['phone' => $request->phone],
                [
                    'full_name' => $request->full_name,
                    'email'     => $request->email ?? null,
                ]
            );

            $service = Service::findOrFail($request->service_id);
            $price = $request->is_premium && $service->price_premium ? $service->price_premium : $service->price_regular;

            Appointment::create([
                'customer_id'    => $customer->id,
                'staff_id'       => $request->staff_id,
                'service_id'     => $service->id,
                'start_datetime' => $start,
                'end_datetime'   => Carbon::parse($start)->addMinutes($service->duration_minutes),
                'status'         => 'scheduled',
                'total_amount'   => $price,
                'is_walk_in'     => false,
                'notes'          => $request->notes,
            ]);
        });

        return redirect()->back()->with('success', 'Appointment booked successfully! We will contact you shortly.');
    }
}