<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use App\Models\SalonSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerBookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'full_name'       => 'required|string|max:100',
            'phone'           => 'required|string|max:20',
            'email'           => 'nullable|email',
            'service_id'      => 'required|exists:services,id',
            'staff_id'        => 'required|exists:staff,id',
            'appointment_date'=> 'required|date|after_or_equal:today',
            'appointment_time'=> 'required', // Should be in H:i format (e.g., 14:30)
        ]);

        // Combine date and time
        $startDateTimeString = $request->appointment_date . ' ' . $request->appointment_time . ':00';
        $startDateTime = Carbon::parse($startDateTimeString);

        // Get salon settings
        $salonSettings = SalonSetting::getSettings();

        // Validate business hours
        $startTime = $startDateTime->format('H:i:s');
        if ($startTime < $salonSettings->opening_time || $startTime > $salonSettings->closing_time) {
            return back()->withErrors([
                'appointment_time' => 'Selected time is outside business hours: ' .
                    Carbon::createFromFormat('H:i:s', $salonSettings->opening_time)->format('g:i A') . ' - ' .
                    Carbon::createFromFormat('H:i:s', $salonSettings->closing_time)->format('g:i A')
            ]);
        }

        // Validate max days ahead
        $maxDate = now()->addDays($salonSettings->max_days_book_ahead);
        if ($startDateTime->gt($maxDate)) {
            return back()->withErrors([
                'appointment_date' => "Appointments can only be booked up to {$salonSettings->max_days_book_ahead} days in advance."
            ]);
        }

        // Get service and duration
        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration_minutes;

        // Minimum duration check
        if ($duration < 30) {
            return back()->withErrors([
                'service_id' => 'Selected service duration is too short (minimum 30 minutes).'
            ]);
        }

        // Calculate end time
        $endDateTime = $startDateTime->copy()->addMinutes($duration);

        // Ensure appointment ends before or at closing time
        $closingTimeToday = Carbon::parse($request->appointment_date . ' ' . $salonSettings->closing_time);
        if ($endDateTime->gt($closingTimeToday)) {
            return back()->withErrors([
                'appointment_time' => 'This appointment would end after closing time. Please choose an earlier slot.'
            ]);
        }

        // Check for overlapping appointments (proper availability check)
        $isBooked = Appointment::where('staff_id', $request->staff_id)
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('start_datetime', [$startDateTime, $endDateTime->copy()->subMinute()])
                  ->orWhereBetween('end_datetime', [$startDateTime->copy()->addMinute(), $endDateTime])
                  ->orWhere(function ($q2) use ($startDateTime, $endDateTime) {
                      $q2->where('start_datetime', '<=', $startDateTime)
                         ->where('end_datetime', '>=', $endDateTime);
                  });
            })
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->exists();

        if ($isBooked) {
            return back()->withErrors([
                'appointment_time' => 'This staff member is not available for the selected time slot. Please choose another time.'
            ]);
        }

        // All checks passed — create customer and appointment in transaction
        DB::transaction(function () use ($request, $startDateTimeString, $service, $endDateTime) {
            $customer = Customer::firstOrCreate(
                ['phone' => $request->phone],
                [
                    'full_name' => $request->full_name,
                    'email'     => $request->email ?? null,
                ]
            );

            $price = ($request->has('is_premium') && $request->is_premium && $service->price_premium)
                ? $service->price_premium
                : $service->price_regular;

            Appointment::create([
                'customer_id'    => $customer->id,
                'staff_id'       => $request->staff_id,
                'service_id'     => $service->id,
                'start_datetime' => $startDateTimeString,
                'end_datetime'   => $endDateTime,
                'status'         => 'scheduled',
                'total_amount'   => $price,
                'is_walk_in'     => false,
                'notes'          => $request->notes ?? null,
            ]);
        });

        return redirect()->back()->with('success', 'Appointment booked successfully! We will contact you shortly.');
    }
}