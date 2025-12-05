@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Reports Dashboard</h1>
        <div class="text-sm text-gray-600">
            <i class="fas fa-calendar-alt mr-2"></i>
            {{ now()->format('F j, Y') }}
        </div>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Appointment Reports -->
        <div class="card cursor-pointer hover:shadow-xl transition-all duration-300" 
             onclick="location.href='{{ route('dashboard.reports.appointments') }}'">
            <div class="flex items-center">
                <div class="bg-blue-100 p-4 rounded-xl mr-4">
                    <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Appointment Reports</h3>
                    <p class="text-gray-600 text-sm">View daily, weekly & monthly appointment analytics</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-chart-line mr-2"></i>
                    Status breakdown, service popularity, staff performance
                </div>
            </div>
        </div>

        <!-- Revenue Reports -->
        <div class="card cursor-pointer hover:shadow-xl transition-all duration-300"
             onclick="location.href='{{ route('dashboard.reports.revenue') }}'">
            <div class="flex items-center">
                <div class="bg-green-100 p-4 rounded-xl mr-4">
                    <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Revenue Reports</h3>
                    <p class="text-gray-600 text-sm">Track income, payment methods & service revenue</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Revenue breakdown, trends & payment analytics
                </div>
            </div>
        </div>

        <!-- Inventory Reports -->
        <div class="card cursor-pointer hover:shadow-xl transition-all duration-300"
             onclick="location.href='{{ route('dashboard.reports.inventory') }}'">
            <div class="flex items-center">
                <div class="bg-gray-100 p-4 rounded-xl mr-4">
                    <i class="fas fa-boxes text-gray-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Inventory Reports</h3>
                    <p class="text-gray-600 text-sm">Monitor stock levels, usage & low stock alerts</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Stock updates, low stock items & usage patterns
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $today = now()->toDateString();
                $todayAppointments = \App\Models\Appointment::whereDate('start_datetime', $today)->count();
                $todayRevenue = \App\Models\Appointment::where('status', 'completed')
                    ->whereDate('start_datetime', $today)
                    ->sum('total_amount');
                $lowStockItems = \App\Models\InventoryItem::whereRaw('current_stock <= minimum_stock')->count();
                $pendingPayments = \App\Models\Payment::where('status', 'pending')->count();
            @endphp
            
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $todayAppointments }}</div>
                <div class="text-sm text-gray-600">Today's Appointments</div>
            </div>
            
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">₱{{ number_format($todayRevenue, 2) }}</div>
                <div class="text-sm text-gray-600">Today's Revenue</div>
            </div>
            
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ $lowStockItems }}</div>
                <div class="text-sm text-gray-600">Low Stock Items</div>
            </div>
            
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $pendingPayments }}</div>
                <div class="text-sm text-gray-600">Pending Payments</div>
            </div>
        </div>
    </div>

<!-- Recent Reports -->
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Recent Reports Generated</h2>
        <span class="text-sm text-gray-500">
            {{ $recentReports->count() }} reports
        </span>
    </div>
    
    @if($recentReports->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Report Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Range</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Generated</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Records</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Generated By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentReports as $report)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    @switch($report->report_type)
                                        @case('appointments')
                                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                            <span class="text-sm font-medium text-gray-900">Appointments</span>
                                            @break
                                        @case('revenue')
                                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                            <span class="text-sm font-medium text-gray-900">Revenue</span>
                                            @break
                                        @case('inventory')
                                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                                            <span class="text-sm font-medium text-gray-900">Inventory</span>
                                            @break
                                    @endswitch
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $report->date_range_label }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $report->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                    {{ $report->record_count }} records
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $report->user->full_name ?? 'System' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <button onclick="regenerateReport('{{ $report->report_type }}', '{{ $report->start_date }}', '{{ $report->end_date }}')" 
                                        class="text-blue-600 hover:text-black-900 mr-3">
                                    <i class="fas fa-download mr-1"></i> Download again
                                </button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($recentReports->count() >= 10)
            <div class="mt-4 text-center">
                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All Reports <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        @endif
    @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-file-alt text-4xl mb-4"></i>
            <p>No recent reports generated yet.</p>
            <p class="text-sm mt-2">Generate your first report to see it here.</p>
        </div>
    @endif
</div>

<script>
function regenerateReport(type, startDate, endDate) {
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
    typeInput.value = type;
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
</script>
@endsection