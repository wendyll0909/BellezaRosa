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
            'method' => 'required|in:cash,gcash,bank_transfer',
            'amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'payment_details' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);

        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'customer_id' => $appointment->customer_id,
            'amount' => $request->amount,
            'method' => $request->method,
            'reference_number' => $request->reference_number,
            'payment_details' => $request->payment_details,
            'status' => $request->method === 'cash' ? 'paid' : 'pending',
            'notes' => $request->notes,
            'paid_at' => $request->method === 'cash' ? now() : null
        ]);

        // Update appointment payment status
        $appointment->update([
            'payment_method' => $request->method
        ]);

        return redirect()->route('dashboard.payments.index')
            ->with('success', 'Payment recorded successfully!');
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,failed,refunded',
            'reference_number' => 'nullable|string|max:100'
        ]);

        $payment->update([
            'status' => $request->status,
            'reference_number' => $request->reference_number,
            'paid_at' => $request->status === 'paid' ? now() : null
        ]);

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