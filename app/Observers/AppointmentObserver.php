<?php
// [file name]: AppointmentObserver.php - Update
namespace App\Observers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Payment;

class AppointmentObserver
{
    public function creating(Appointment $appointment)
    {
        if (!$appointment->end_datetime) {
            $duration = Service::find($appointment->service_id)->duration_minutes ?? 60;
            $appointment->end_datetime = $appointment->start_datetime->copy()->addMinutes($duration);
        }
    }

    public function created(Appointment $appointment)
    {
        // Automatically create a pending payment when appointment is created
        Payment::create([
            'appointment_id' => $appointment->id,
            'customer_id' => $appointment->customer_id,
            'amount' => $appointment->total_amount,
            'method' => $appointment->payment_method,
            'status' => 'pending',
            'notes' => 'Automatically created with appointment'
        ]);
    }

    public function updated(Appointment $appointment)
    {
        if ($appointment->isDirty('status') && $appointment->status === 'completed') {
            $customer = Customer::find($appointment->customer_id);
            $customer->increment('total_visits');
            $customer->increment('total_spent', $appointment->total_amount);
            $customer->update(['last_visit' => $appointment->start_datetime]);

            // Mark payment as paid when appointment is completed
            $payment = Payment::where('appointment_id', $appointment->id)->first();
            if ($payment && $payment->status === 'pending') {
                $payment->markAsPaid();
            }
        }
    }
}