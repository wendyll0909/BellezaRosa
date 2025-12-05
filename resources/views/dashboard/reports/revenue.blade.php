@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Revenue Reports</h1>
            <p class="text-gray-600">Track income, payments, and revenue trends</p>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="downloadReport()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-download mr-2"></i> Download Report
            </button>
            <button onclick="location.href='{{ route('dashboard.reports.index') }}'" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </button>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="card mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" id="startDate" value="{{ $startDate }}" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" id="endDate" value="{{ $endDate }}" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="flex items-end">
                <button onclick="loadReport()" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg">
                    <i class="fas fa-filter mr-2"></i> Apply Filter
                </button>
            </div>
            <div class="flex items-end">
                <button onclick="resetDates()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg">
                    <i class="fas fa-redo mr-2"></i> Reset
                </button>
            </div>
        </div>
        <div class="mt-4 text-sm text-gray-600">
            <i class="fas fa-info-circle mr-2"></i>
            Showing data from <span class="font-medium">{{ $data['date_range_label'] ?? 'selected period' }}</span>
            | Total Revenue: <span class="font-medium text-green-600">₱{{ number_format($data['total_revenue'] ?? 0, 2) }}</span>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-green-600">₱{{ number_format($data['total_revenue'] ?? 0, 2) }}</div>
            <div class="text-sm text-gray-600">Total Revenue</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $data['completed_appointments']->count() ?? 0 }}</div>
            <div class="text-sm text-gray-600">Completed Services</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $data['payments']->count() ?? 0 }}</div>
            <div class="text-sm text-gray-600">Payment Transactions</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">
                {{ $data['service_revenue']->count() ?? 0 }}
            </div>
            <div class="text-sm text-gray-600">Services Revenue</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue by Service -->
        <div class="card lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue by Service</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Percentage</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chart</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(isset($data['service_revenue']) && $data['service_revenue']->count() > 0)
                            @foreach($data['service_revenue']->sortByDesc(function($value) { return $value; }) as $service => $revenue)
                                @php
                                    $percentage = $data['total_revenue'] > 0 ? ($revenue / $data['total_revenue']) * 100 : 0;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $service }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-green-600">
                                        ₱{{ number_format($revenue, 2) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($percentage, 1) }}%
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No revenue data available for selected period</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Method Breakdown</h3>
            <div class="space-y-4">
                @if(isset($data['payment_method_breakdown']) && $data['payment_method_breakdown']->count() > 0)
                    @foreach($data['payment_method_breakdown'] as $method => $amount)
                        @php
                            $percentage = $data['total_revenue'] > 0 ? ($amount / $data['total_revenue']) * 100 : 0;
                            $color = match($method) {
                                'cash' => 'bg-green-500',
                                'gcash' => 'bg-blue-500',
                                'bank_transfer' => 'bg-purple-500',
                                default => 'bg-gray-500'
                            };
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                                <span class="text-gray-600">₱{{ number_format($amount, 2) }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">No payment data available</div>
                @endif
            </div>
            
            <!-- Daily Revenue Chart -->
            @if(isset($data['daily_revenue']) && $data['daily_revenue']->count() > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-md font-semibold text-gray-700 mb-3">Daily Revenue</h4>
                    <div class="space-y-2">
                        @foreach($data['daily_revenue'] as $date => $revenue)
                            <div class="flex items-center">
                                <div class="w-24 text-sm text-gray-600">{{ \Carbon\Carbon::parse($date)->format('M d') }}</div>
                                <div class="flex-1 ml-2">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span>₱{{ number_format($revenue, 2) }}</span>
                                        @php
                                            $maxRevenue = $data['daily_revenue']->max();
                                            $barWidth = $maxRevenue > 0 ? ($revenue / $maxRevenue) * 100 : 0;
                                        @endphp
                                        <span>{{ number_format($barWidth, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $barWidth }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Revenue Details -->
    <div class="card mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Transactions</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(isset($data['payments']) && $data['payments']->count() > 0)
                        @foreach($data['payments'] as $payment)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->created_at->format('M d, h:i A') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $payment->customer->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->appointment->service->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->appointment->staff->user->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->customer->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $payment->method == 'cash' ? 'bg-green-100 text-green-800' : 
                                           ($payment->method == 'gcash' ? 'bg-blue-100 text-blue-800' : 
                                           'bg-purple-100 text-purple-800') }}">
                                        {{ ucfirst($payment->method) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800' : 
                                           ($payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($payment->status == 'failed' ? 'bg-red-100 text-red-800' : 
                                           'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ₱{{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No payment transactions found for selected period</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function loadReport() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        alert('Start date cannot be after end date');
        return;
    }
    
    // Show loading
    const loading = document.createElement('div');
    loading.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loading.innerHTML = '<div class="bg-white p-6 rounded-lg shadow-xl"><i class="fas fa-spinner fa-spin text-2xl text-green-600"></i></div>';
    document.body.appendChild(loading);
    
    // Load data via AJAX
    fetch(`{{ route('dashboard.reports.revenue') }}?start_date=${startDate}&end_date=${endDate}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .finally(() => {
            document.body.removeChild(loading);
        });
}

function downloadReport() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        alert('Start date cannot be after end date');
        return;
    }
    
    // Create form dynamically
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("dashboard.reports.download") }}';
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add type
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = 'revenue';
    form.appendChild(typeInput);
    
    // Add start date
    const startInput = document.createElement('input');
    startInput.type = 'hidden';
    startInput.name = 'start_date';
    startInput.value = startDate;
    form.appendChild(startInput);
    
    // Add end date
    const endInput = document.createElement('input');
    endInput.type = 'hidden';
    endInput.name = 'end_date';
    endInput.value = endDate;
    form.appendChild(endInput);
    
    // Submit form
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function resetDates() {
    const today = new Date().toISOString().split('T')[0];
    const weekAgo = new Date();
    weekAgo.setDate(weekAgo.getDate() - 7);
    
    document.getElementById('startDate').value = weekAgo.toISOString().split('T')[0];
    document.getElementById('endDate').value = today;
    loadReport();
}

// Set default dates if not set
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('startDate').value) {
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        document.getElementById('startDate').value = weekAgo.toISOString().split('T')[0];
    }
    
    if (!document.getElementById('endDate').value) {
        document.getElementById('endDate').value = new Date().toISOString().split('T')[0];
    }
});
</script>
@endsection