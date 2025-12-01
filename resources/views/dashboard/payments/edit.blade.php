<!-- [file name]: resources/views/dashboard/payments/edit.blade.php -->
@extends('layouts.dashboard')

@section('title', 'Edit Payment - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Edit Payment #{{ $payment->id }}</h1>
        <a href="{{ route('dashboard.payments.show', $payment) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-xl transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Payment
        </a>
    </div>

    <div class="card">
        <form action="{{ route('dashboard.payments.update', $payment) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Method -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Payment Method *</label>
                    <select name="method" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                        <option value="cash" {{ $payment->method == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="gcash" {{ $payment->method == 'gcash' ? 'selected' : '' }}>GCash</option>
                        <option value="bank_transfer" {{ $payment->method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>

                <!-- Amount -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Amount *</label>
                    <input type="number" step="0.01" name="amount" value="{{ $payment->amount }}" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Status *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                        <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $payment->status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ $payment->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <!-- Reference Number -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Reference Number</label>
                    <input type="text" name="reference_number" value="{{ $payment->reference_number }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                           placeholder="Reference number for GCash/Bank Transfer">
                </div>

                <!-- Notes -->
                <div class="form-group md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="Payment notes...">{{ $payment->notes }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('dashboard.payments.show', $payment) }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                    <i class="fas fa-save mr-2"></i> Update Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection