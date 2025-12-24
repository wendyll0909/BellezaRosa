@extends('layouts.dashboard')

@section('title', 'Commissions - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Commission Management</h1>
        <div class="flex space-x-3">
            <a href="{{ route('dashboard.commissions.report') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl transition">
                <i class="fas fa-chart-bar mr-2"></i> Generate Report
            </a>
            <a href="{{ route('dashboard.commissions.settings') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-xl transition">
                <i class="fas fa-cog mr-2"></i> Settings
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Commission -->
        <div class="card border-l-4 border-blue-500">
            <div class="flex">
                <div class="p-3 bg-blue-100 rounded-xl h-fit">
                    <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Total Commission</h3>
                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($totalCommission, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Commission -->
        <div class="card border-l-4 border-yellow-500">
            <div class="flex">
                <div class="p-3 bg-yellow-100 rounded-xl h-fit">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Pending Commission</h3>
                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($totalPending, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Paid Commission -->
        <div class="card border-l-4 border-green-500">
            <div class="flex">
                <div class="p-3 bg-green-100 rounded-xl h-fit">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Paid Commission</h3>
                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($totalPaid, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <form action="{{ route('dashboard.commissions.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col md:flex-row gap-4 flex-1">
                <!-- Staff Filter -->
                <select name="staff_id" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none w-full md:w-48">
                    <option value="">All Staff</option>
                    @foreach($staff as $staffMember)
                        <option value="{{ $staffMember->id }}" {{ request('staff_id') == $staffMember->id ? 'selected' : '' }}>
                            {{ $staffMember->user->full_name }}
                        </option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <!-- Month Filter -->
                <select name="month" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    <option value="">All Months</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>

                <!-- Year Filter -->
                <input type="number" name="year" value="{{ request('year', date('Y')) }}" 
                       min="2020" max="{{ date('Y') + 5 }}"
                       class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none w-full md:w-32">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl transition">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
                
                @if(request()->anyFilled(['staff_id', 'status', 'month', 'year']))
                <a href="{{ route('dashboard.commissions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-xl transition">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Commissions Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Staff</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Service</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Service Amount</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Commission Rate</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Commission Amount</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($commissions as $commission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $commission->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $commission->staff->user->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ ucfirst($commission->staff->specialty) }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $commission->appointment->service->name }}
                            <div class="text-xs text-gray-500">{{ $commission->appointment->customer->full_name }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                            ₱{{ number_format($commission->service_amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ number_format($commission->commission_rate, 1) }}%
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-green-600">
                            ₱{{ number_format($commission->amount, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $commission->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $commission->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $commission->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($commission->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <a href="{{ route('dashboard.commissions.show', $commission) }}" 
                                   class="text-blue-600 hover:text-blue-800" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($commission->status == 'pending')
                                <form action="{{ route('dashboard.commissions.pay', $commission) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800" title="Mark as Paid">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-money-bill-wave text-4xl mb-3 text-gray-300"></i>
                            <p>No commissions found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($commissions->hasPages())
        <div class="mt-6">
            {{ $commissions->links() }}
        </div>
        @endif
    </div>

    <!-- Bulk Payment Section -->
    @if($totalPending > 0)
    <div class="card">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Bulk Commission Payment</h3>
        <form id="bulkPaymentForm" action="{{ route('dashboard.commissions.bulk-pay') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Payment Date *</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                </div>
                
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Payment Method *</label>
                    <select name="payment_method" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="gcash">GCash</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Reference Number</label>
                    <input type="text" name="reference_number"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                           placeholder="Optional reference number">
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-gray-700 font-semibold mb-2">Select Commissions to Pay</label>
                <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach($commissions->where('status', 'pending') as $commission)
                    <div class="flex items-center mb-2 p-2 hover:bg-gray-50 rounded">
                        <input type="checkbox" name="commission_ids[]" value="{{ $commission->id }}" 
                               class="mr-3 h-4 w-4 text-blue-600 rounded">
                        <div class="flex-1">
                            <div class="font-medium">{{ $commission->staff->user->full_name }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $commission->appointment->service->name }} - 
                                ₱{{ number_format($commission->amount, 2) }}
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $commission->created_at->format('M j') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="selectAllPending()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                    Select All Pending
                </button>
                <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                    <i class="fas fa-money-check-alt mr-2"></i> Pay Selected Commissions
                </button>
            </div>
        </form>
    </div>
    @endif
</div>

<script>
function selectAllPending() {
    document.querySelectorAll('input[name="commission_ids[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

// Auto-select commission when clicking row
document.querySelectorAll('.hover\\:bg-gray-50').forEach(row => {
    const checkbox = row.querySelector('input[type="checkbox"]');
    if (checkbox) {
        row.addEventListener('click', (e) => {
            if (e.target !== checkbox && !checkbox.contains(e.target)) {
                checkbox.checked = !checkbox.checked;
            }
        });
    }
});
</script>
@endsection