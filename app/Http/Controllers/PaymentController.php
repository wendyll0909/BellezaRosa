<?php
// [file name]: PaymentController.php
namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['appointment.service', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['appointment.service', 'appointment.staff.user', 'customer']);
        return view('dashboard.payments.show', compact('payment'));
    }

    public function createForAppointment(Appointment $appointment)
    {
        return view('dashboard.payments.create', compact('appointment'));
    }

    public function store(Request $request)
{
    $request->validate([
        'appointment_id' => 'required|exists:appointments,id',
        'method' => 'required|in:cash,gcash,bank_transfer,online',
        'amount' => 'required|numeric|min:0',
        'reference_number' => 'nullable|string|max:100',
        'payment_details' => 'nullable|array',
        'notes' => 'nullable|string',
        'status' => 'required|in:pending,paid'  // Add status field
    ]);

    $appointment = Appointment::findOrFail($request->appointment_id);

    // Check if payment already exists
    $existingPayment = Payment::where('appointment_id', $appointment->id)->first();
    
    DB::transaction(function () use ($request, $appointment, $existingPayment) {
        if ($existingPayment) {
            // Update existing payment
            $existingPayment->update([
                'amount' => $request->amount,
                'method' => $request->method,
                'reference_number' => $request->reference_number,
                'payment_details' => $request->payment_details,
                'status' => $request->status,
                'notes' => $request->notes,
                'paid_at' => $request->status === 'paid' ? now() : null
            ]);
            
            $payment = $existingPayment;
        } else {
            // Create new payment
            $payment = Payment::create([
                'appointment_id' => $appointment->id,
                'customer_id' => $appointment->customer_id,
                'amount' => $request->amount,
                'method' => $request->method,
                'reference_number' => $request->reference_number,
                'payment_details' => $request->payment_details,
                'status' => $request->status,
                'notes' => $request->notes,
                'paid_at' => $request->status === 'paid' ? now() : null
            ]);
        }

        // Update appointment payment method
        $appointment->update([
            'payment_method' => $request->method
        ]);
        
        // If payment is marked as paid, also mark appointment as completed if it's not already
        if ($request->status === 'paid' && $appointment->status !== 'completed') {
            $appointment->update(['status' => 'completed']);
        }
    });

    return redirect()->route('dashboard.payments.index')
        ->with('success', 'Payment recorded successfully!');
}

    public function updateStatus(Request $request, Payment $payment)
{
    $request->validate([
        'status' => 'required|in:pending,paid,failed,refunded',
        'reference_number' => 'nullable|string|max:100',
        'cancellation_reason' => 'required_if:status,failed,cancelled|nullable|string|max:255' // Add cancellation reason
    ]);

    DB::transaction(function () use ($request, $payment) {
        $oldStatus = $payment->status;
        $newStatus = $request->status;
        
        $payment->update([
            'status' => $newStatus,
            'reference_number' => $request->reference_number,
            'paid_at' => $newStatus === 'paid' ? now() : $payment->paid_at,
            'notes' => $request->notes ?: $payment->notes
        ]);
        
        // Update appointment status based on payment status
        $appointment = $payment->appointment;
        if ($newStatus === 'paid' && $appointment->status !== 'completed') {
            $appointment->update(['status' => 'completed']);
        } elseif ($newStatus === 'failed' || $newStatus === 'refunded') {
            // Add cancellation reason to appointment notes
            if ($request->cancellation_reason) {
                $currentNotes = $appointment->notes ? $appointment->notes . "\n" : '';
                $appointment->update([
                    'status' => 'cancelled',
                    'notes' => $currentNotes . "Payment " . $newStatus . ": " . $request->cancellation_reason
                ]);
            }
        }
    });

    return back()->with('success', 'Payment status updated!');
}

    public function edit(Payment $payment)
    {
        $payment->load(['appointment', 'customer']);
        return view('dashboard.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'method' => 'required|in:cash,gcash,bank_transfer',
            'amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'status' => 'required|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string'
        ]);

        $payment->update([
            'method' => $request->method,
            'amount' => $request->amount,
            'reference_number' => $request->reference_number,
            'status' => $request->status,
            'paid_at' => $request->status === 'paid' ? now() : null,
            'notes' => $request->notes
        ]);

        return redirect()->route('dashboard.payments.show', $payment)
            ->with('success', 'Payment updated successfully!');
    }
}