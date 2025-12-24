<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancellationController extends Controller
{
    public function cancelAppointment(Request $request, Appointment $appointment)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
            'refund_amount' => 'nullable|numeric|min:0',
            'refund_method' => 'nullable|in:cash,gcash,bank_transfer',
            'status' => 'required|in:cancelled,failed' // Distinguish between cancelled and failed
        ]);

        DB::transaction(function () use ($request, $appointment) {
            $status = $request->status;
            $reason = $request->cancellation_reason;
            
            if ($status === 'cancelled') {
                $appointment->cancel($reason, auth()->user());
            } else {
                $appointment->markAsFailed($reason);
            }
            
            // Handle refund if applicable
            if ($request->refund_amount > 0 && $appointment->payment) {
                $this->processRefund(
                    $appointment->payment,
                    $request->refund_amount,
                    $request->refund_method,
                    $reason
                );
            }
            
            // Log the cancellation
            activity()
                ->performedOn($appointment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'reason' => $reason,
                    'status' => $status,
                    'refund_amount' => $request->refund_amount
                ])
                ->log("Appointment {$status}");
        });

        return redirect()->route('dashboard.appointments.index')
            ->with('success', "Appointment has been {$request->status} successfully.");
    }
    
    private function processRefund(Payment $payment, $amount, $method, $reason)
    {
        // Create a refund record
        $refund = Payment::create([
            'appointment_id' => $payment->appointment_id,
            'customer_id' => $payment->customer_id,
            'amount' => -$amount, // Negative amount for refund
            'method' => $method,
            'status' => 'refunded',
            'notes' => "Refund for cancelled appointment: " . $reason,
            'paid_at' => now()
        ]);
        
        // Update original payment status if fully refunded
        if ($amount >= $payment->amount) {
            $payment->update(['status' => 'refunded']);
        }
        
        return $refund;
    }
    
    public function showCancellationForm(Appointment $appointment)
    {
        return view('dashboard.appointments.cancel', compact('appointment'));
    }
}