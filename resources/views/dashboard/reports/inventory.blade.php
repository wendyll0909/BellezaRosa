@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Inventory Reports</h1>
            <p class="text-gray-600">Monitor stock levels, usage, and updates</p>
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
                <input type="date" id="startDate" value="{{ $startDate }}" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" id="endDate" value="{{ $endDate }}" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
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
            <div class="text-2xl font-bold text-purple-600">{{ $data['total_items'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Total Items</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $data['low_stock_count'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Low Stock Items</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $data['updates']->count() ?? 0 }}</div>
            <div class="text-sm text-gray-600">Stock Updates</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-green-600">
                @if(isset($data['update_types']['add']))
                    {{ $data['update_types']['add']['total_quantity'] }}
                @else
                    0
                @endif
            </div>
            <div class="text-sm text-gray-600">Items Added</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Low Stock Alert -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Low Stock Alert</h3>
                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                    {{ $data['low_stock_count'] ?? 0 }} Items
                </span>
            </div>
            
            <div class="space-y-3">
                @if(isset($data['low_stock_items']) && $data['low_stock_items']->count() > 0)
                    @foreach($data['low_stock_items'] as $item)
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-medium text-red-800">{{ $item->name }}</div>
                                    <div class="text-sm text-red-600">
                                        Stock: {{ $item->current_stock }} {{ $item->unit }} | 
                                        Min: {{ $item->minimum_stock }} {{ $item->unit }}
                                    </div>
                                </div>
                                <div class="text-2xl font-bold text-red-600">
                                    @php
                                        $percentage = $item->minimum_stock > 0 ? ($item->current_stock / $item->minimum_stock) * 100 : 0;
                                    @endphp
                                    {{ number_format($percentage, 0) }}%
                                </div>
                            </div>
                            <div class="mt-2 w-full bg-red-200 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-6">
                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                        <p class="text-gray-600">All items are sufficiently stocked</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Update Types Breakdown -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Activity</h3>
            <div class="space-y-4">
                @if(isset($data['update_types']) && $data['update_types']->count() > 0)
                    @foreach($data['update_types'] as $type => $stats)
                        @php
                            $color = match($type) {
                                'add' => 'bg-green-500',
                                'subtract' => 'bg-red-500',
                                'set' => 'bg-blue-500',
                                default => 'bg-gray-500'
                            };
                            $totalUpdates = $data['updates']->count();
                            $percentage = $totalUpdates > 0 ? ($stats['count'] / $totalUpdates) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <div>
                                    <span class="font-medium text-gray-700">{{ ucfirst($type) }} Updates</span>
                                    <span class="text-gray-500 ml-2">{{ $stats['count'] }} updates</span>
                                </div>
                                <span class="text-gray-600">{{ $stats['total_quantity'] }} units</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">No update activity for selected period</div>
                @endif
            </div>
            
            <!-- Most Active Items -->
            @if(isset($data['most_updated_items']) && $data['most_updated_items']->count() > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-md font-semibold text-gray-700 mb-3">Most Updated Items</h4>
                    <div class="space-y-2">
                        @foreach($data['most_updated_items'] as $itemName => $stats)
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                <span class="text-sm text-gray-700 truncate">{{ $itemName }}</span>
                                <div class="flex items-center space-x-3">
                                    <span class="text-xs text-gray-500">{{ $stats['count'] }} updates</span>
                                    <span class="text-xs font-medium text-purple-600">{{ $stats['total_quantity'] }} units</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Stock Status Overview -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Stock Status Overview</h3>
            <div class="space-y-3">
                @php
                    $items = $data['items'] ?? collect();
                    $healthyItems = $items->where('current_stock', '>', DB::raw('minimum_stock * 1.5'))->count();
                    $warningItems = $items->where('current_stock', '>', DB::raw('minimum_stock'))
                                          ->where('current_stock', '<=', DB::raw('minimum_stock * 1.5'))
                                          ->count();
                    $criticalItems = $data['low_stock_count'] ?? 0;
                    $totalItems = $items->count();
                @endphp
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-green-600">Healthy Stock</span>
                        <span class="text-gray-600">{{ $healthyItems }} items</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalItems > 0 ? ($healthyItems / $totalItems) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-yellow-600">Warning Level</span>
                        <span class="text-gray-600">{{ $warningItems }} items</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $totalItems > 0 ? ($warningItems / $totalItems) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-red-600">Critical (Low Stock)</span>
                        <span class="text-gray-600">{{ $criticalItems }} items</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ $totalItems > 0 ? ($criticalItems / $totalItems) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-xl font-bold text-gray-800">
                            @php
                                $avgStock = $items->avg('current_stock');
                            @endphp
                            {{ number_format($avgStock ?? 0, 1) }}
                        </div>
                        <div class="text-xs text-gray-600">Avg Stock per Item</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-xl font-bold text-gray-800">
                            @php
                                $totalStock = $items->sum('current_stock');
                            @endphp
                            {{ number_format($totalStock ?? 0) }}
                        </div>
                        <div class="text-xs text-gray-600">Total Items in Stock</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Updates Table -->
    <div class="card mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Inventory Updates</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Previous</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">New Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Updated By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remark</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(isset($data['updates']) && $data['updates']->count() > 0)
                        @foreach($data['updates'] as $update)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $update->created_at->format('M d, h:i A') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $update->item->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $update->type == 'add' ? 'bg-green-100 text-green-800' : 
                                           ($update->type == 'subtract' ? 'bg-red-100 text-red-800' : 
                                           'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($update->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium 
                                    {{ $update->type == 'add' ? 'text-green-600' : 
                                       ($update->type == 'subtract' ? 'text-red-600' : 'text-blue-600') }}">
                                    {{ $update->quantity }} {{ $update->item->unit ?? '' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $update->previous_stock }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $update->new_stock }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $update->updatedBy->full_name ?? 'System' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate">
                                    {{ $update->remark }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No inventory updates found for selected period</td>
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
    loading.innerHTML = '<div class="bg-white p-6 rounded-lg shadow-xl"><i class="fas fa-spinner fa-spin text-2xl text-purple-600"></i></div>';
    document.body.appendChild(loading);
    
    // Load data via AJAX
    fetch(`{{ route('dashboard.reports.inventory') }}?start_date=${startDate}&end_date=${endDate}`)
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
    typeInput.value = 'inventory';
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