<!-- [file name]: resources/views/dashboard/payments/create.blade.php -->
@extends('layouts.dashboard')

@section('title', 'Create Payment - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Record Payment</h1>
        <a href="{{ route('dashboard.payments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-xl transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Payments
        </a>
    </div>

    <div class="card">
        <form action="{{ route('dashboard.payments.store') }}" method="POST">
            @csrf
            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Appointment Info -->
                <div class="md:col-span-2 p-4 bg-blue-50 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Appointment Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium">Customer:</span> {{ $appointment->customer->full_name }}
                        </div>
                        <div>
                            <span class="font-medium">Service:</span> {{ $appointment->service->name }}
                        </div>
                        <div>
                            <span class="font-medium">Date:</span> {{ $appointment->start_datetime->format('M j, Y g:i A') }}
                        </div>
                        <div>
                            <span class="font-medium">Amount Due:</span> ₱{{ number_format($appointment->total_amount, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Payment Method *</label>
                    <select name="method" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>

                <!-- Amount -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Amount *</label>
                    <input type="number" step="0.01" name="amount" value="{{ $appointment->total_amount }}" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                </div>

                <!-- Reference Number (Conditional) -->
                <div id="referenceField" class="form-group hidden">
                    <label class="block text-gray-700 font-semibold mb-2" id="referenceLabel">Reference Number *</label>
                    <input type="text" name="reference_number" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                           placeholder="Enter reference number">
                </div>

                <!-- Notes -->
                <div class="form-group md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="Additional payment notes..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('dashboard.payments.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                    <i class="fas fa-money-bill-wave mr-2"></i> Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('select[name="method"]').addEventListener('change', function() {
    const referenceField = document.getElementById('referenceField');
    const referenceLabel = document.getElementById('referenceLabel');
    const referenceInput = document.querySelector('input[name="reference_number"]');
    
    if (this.value === 'gcash') {
        referenceField.classList.remove('hidden');
        referenceLabel.textContent = 'GCash Reference Number *';
        referenceInput.required = true;
    } else if (this.value === 'bank_transfer') {
        referenceField.classList.remove('hidden');
        referenceLabel.textContent = 'Bank Reference Number *';
        referenceInput.required = true;
    } else {
        referenceField.classList.add('hidden');
        referenceInput.required = false;
        referenceInput.value = '';
    }
});
</script>
@endsection