@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Appointment Reports</h1>
            <p class="text-gray-600">Analyze appointment data with date range filters</p>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="downloadReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
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
                <input type="date" id="startDate" value="{{ $startDate }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" id="endDate" value="{{ $endDate }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button onclick="loadReport()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">
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
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $data['total'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Total Appointments</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $data['completed'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Completed</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $data['cancelled'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Cancelled</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $data['no_show'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">No Show</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status Breakdown -->
        <div class="card lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Appointment Status Breakdown</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Count</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Percentage</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chart</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(isset($data['status_breakdown']))
                            @foreach($data['status_breakdown'] as $status => $count)
                                @php
                                    $percentage = $data['total'] > 0 ? ($count / $data['total']) * 100 : 0;
                                    $color = match($status) {
                                        'completed' => 'bg-green-500',
                                        'cancelled' => 'bg-red-500',
                                        'no_show' => 'bg-yellow-500',
                                        default => 'bg-blue-500'
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $count }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ number_format($percentage, 1) }}%</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No data available for selected period</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Service Popularity -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Service Popularity</h3>
            <div class="space-y-4">
                @if(isset($data['service_breakdown']))
                    @foreach($data['service_breakdown'] as $service => $count)
                        @php
                            $percentage = $data['total'] > 0 ? ($count / $data['total']) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 truncate">{{ $service }}</span>
                                <span class="text-gray-600">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">No service data available</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Appointment List -->
    <div class="card mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Appointment Details</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(isset($data['appointments']) && $data['appointments']->count() > 0)
                        @foreach($data['appointments'] as $appointment)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $appointment->start_datetime->format('M d, h:i A') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $appointment->customer->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $appointment->service->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $appointment->staff->user->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $appointment->customer->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $appointment->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($appointment->status == 'cancelled' ? 'bg-red-100 text-red-800' : 
                                           ($appointment->status == 'no_show' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ₱{{ number_format($appointment->total_amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No appointments found for selected period</td>
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
    
    // Simple redirect with query parameters
    const url = new URL(window.location.href);
    url.searchParams.set('start_date', startDate);
    url.searchParams.set('end_date', endDate);
    window.location.href = url.toString();
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
    typeInput.value = 'appointments';
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