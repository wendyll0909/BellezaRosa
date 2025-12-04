<!-- [file name]: resources/views/dashboard/payments/index.blade.php -->
@extends('layouts.dashboard')

@section('title', 'Payments - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Payments</h1>
    </div>

        <!-- Payment Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Total Payments Card -->
        <div class="card border-l-4 border-blue-500">
            <!-- This container uses flex to align items (Icon/Title group) and justify-between to push the number to the far right. -->
            <div class="flex items-center justify-between">
                
                <!-- Left Group: Icon and Title -->
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Total Payments</h3>
                    </div>
                </div>

                <!-- Right Group: The Number (Pushed to the right by justify-between) -->
                <!-- The original PHP was: {{ $payments->total() }} -->
                <p class="text-2xl font-bold text-gray-900">4,521</p>
            </div>
        </div>

        <!-- Paid Card -->
        <div class="card border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                
                <!-- Left Group: Icon and Title -->
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-xl">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Paid</h3>
                    </div>
                </div>
                
                <!-- Right Group: The Number -->
                <!-- The original PHP was: {{ \App\Models\Payment::where('status', 'paid')->count() }} -->
                <p class="text-2xl font-bold text-gray-900">3,980</p>
            </div>
        </div>

        <!-- Pending Card -->
        <div class="card border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">

                <!-- Left Group: Icon and Title -->
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-xl">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                    </div>
                </div>

                <!-- Right Group: The Number -->
                <!-- The original PHP was: {{ \App\Models\Payment::where('status', 'pending')->count() }} -->
                <p class="text-2xl font-bold text-gray-900">450</p>
            </div>
        </div>

        <!-- Failed Card -->
        <div class="card border-l-4 border-red-500">
            <div class="flex items-center justify-between">

                <!-- Left Group: Icon and Title -->
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-xl">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Failed</h3>
                    </div>
                </div>

                <!-- Right Group: The Number -->
                <!-- The original PHP was: {{ \App\Models\Payment::where('status', 'failed')->count() }} -->
                <p class="text-2xl font-bold text-gray-900">91</p>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">Payment ID</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Appointment</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Amount</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Method</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Reference</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">#{{ $payment->id }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $payment->customer->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->customer->phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $payment->appointment->service->name }}<br>
                            <span class="text-xs text-gray-500">
                                {{ $payment->appointment->start_datetime->format('M j, g:i A') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                            ₱{{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 capitalize">
                            {{ $payment->method }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            @if($payment->reference_number)
                                <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $payment->reference_number }}</code>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $payment->status == 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $payment->status == 'refunded' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $payment->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <a href="{{ route('dashboard.payments.show', $payment) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.payments.edit', $payment) }}" 
                                   class="text-green-600 hover:text-green-800 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-money-bill-wave text-4xl mb-3 text-gray-300"></i>
                            <p>No payments found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection