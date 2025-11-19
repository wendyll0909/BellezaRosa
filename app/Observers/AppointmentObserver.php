<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Customer;

class AppointmentObserver
{
    public function creating(Appointment $appointment)
    {
        if (!$appointment->end_datetime) {
            $duration = Service::find($appointment->service_id)->duration_minutes ?? 60;
            $appointment->end_datetime = $appointment->start_datetime->copy()->addMinutes($duration);
        }
    }

    public function updated(Appointment $appointment)
    {
        if ($appointment->isDirty('status') && $appointment->status === 'completed') {
            $customer = Customer::find($appointment->customer_id);
            $customer->increment('total_visits');
            $customer->increment('total_spent', $appointment->total_amount);
            $customer->update(['last_visit' => $appointment->start_datetime]);
        }
    }
}