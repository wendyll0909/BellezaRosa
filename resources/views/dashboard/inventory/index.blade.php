<!-- [file name]: resources/views/dashboard/inventory/index.blade.php -->
@extends('layouts.dashboard')

@section('title', 'Inventory - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Inventory</h1>
        <div class="flex space-x-3">
            <a href="{{ route('dashboard.inventory.daily-update') }}" 
               class="bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold py-2 px-4 rounded-xl transition">
                <i class="fas fa-edit mr-2"></i> Daily Update
            </a>
            <button onclick="openAddItemModal()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl transition">
                <i class="fas fa-plus mr-2"></i> Add Item
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card">
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-900">{{ $items->count() }}</div>
                <div class="text-gray-600">Total Items</div>
            </div>
        </div>
        
        <div class="card">
            <div class="text-center">
                <div class="text-3xl font-bold {{ $lowStockCount > 0 ? 'text-yellow-600' : 'text-gray-900' }}">
                    {{ $lowStockCount }}
                </div>
                <div class="text-gray-600">Low Stock Items</div>
            </div>
        </div>
        
        <div class="card">
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-900">{{ $todayUpdates->count() }}</div>
                <div class="text-gray-600">Today's Updates</div>
            </div>
        </div>
    </div>
<!-- Date Filter -->
<div class="card">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Recent Updates</h3>
            <p class="text-gray-600">Stock changes for selected date</p>
        </div>
        
        <!-- Calendar Filter -->
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="date" 
                       id="dateFilter" 
                       value="{{ $selectedDate }}"
                       class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                       max="{{ today()->toDateString() }}">
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-calendar-alt text-gray-400"></i>
                </div>
            </div>
            
            <button onclick="resetDateFilter()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition">
                <i class="fas fa-redo-alt mr-2"></i> Today
            </button>
        </div>
    </div>
    
    <!-- Date Navigation -->
    @if($availableDates->count() > 0)
    <div class="mt-4 pt-4 border-t border-gray-200">
        <div class="flex flex-wrap gap-2">
            <span class="text-sm text-gray-600 mr-2">Quick jump:</span>
            @foreach($availableDates as $date)
                <a href="?date={{ $date }}"
                   class="px-3 py-1 text-sm rounded-lg {{ $date == $selectedDate ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ \Carbon\Carbon::parse($date)->format('M d') }}
                    @if($date == today()->toDateString())
                        <span class="ml-1 text-xs">(Today)</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
    <!-- Inventory Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="px-4 py-3 text-left">Item</th>
                        <th class="px-4 py-3 text-left">Current Stock</th>
                        <th class="px-4 py-3 text-left">Min Stock</th>
                        <th class="px-4 py-3 text-left">Unit</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $item->name }}</div>
                            <div class="text-sm text-gray-500">{{ $item->category }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xl font-bold text-gray-900">{{ $item->current_stock }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->minimum_stock }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->unit }}</td>
                        <td class="px-4 py-3">
                            @if($item->current_stock == 0)
                                <span class="px-2 py-1 text-xs font-semibold  text-red-800">
                                    Out of Stock
                                </span>
                            @elseif($item->isLowStock())
                                <span class="px-2 py-1 text-xs font-semibold  text-yellow-800">
                                    Low Stock
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold  text-green-800">
                                    In Stock
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <button onclick="updateStockModal({{ $item->id }})" 
                                    class="text-blue-600 hover:text-blue-800 transition">
                                <i class="fas fa-edit"></i> Update
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

<!-- Recent Updates -->
@if($todayUpdates->count() > 0)
<div class="card">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gray-900">
            <i class="fas fa-history mr-2"></i>
            Updates for {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}
            @if($selectedDate == today()->toDateString())
                <span class="text-sm font-normal text-green-600 ml-2">(Today)</span>
            @endif
        </h3>
        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
            {{ $todayUpdates->count() }} update(s)
        </span>
    </div>
    
    <div class="space-y-3">
        @foreach($todayUpdates as $update)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <div class="flex-1">
                <div class="font-medium text-gray-900">{{ $update->item->name }}</div>
                <div class="text-sm text-gray-600 mt-1">{{ $update->remark }}</div>
                <div class="text-xs text-gray-500 mt-1">
                    <i class="far fa-clock mr-1"></i>
                    {{ $update->created_at->format('h:i A') }}
                </div>
            </div>
            <div class="text-right">
                <div class="font-bold text-lg {{ $update->type == 'add' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $update->type == 'add' ? '+' : ($update->type == 'subtract' ? '-' : '') }}{{ $update->quantity }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ $update->previous_stock }} → {{ $update->new_stock }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ $update->updatedBy->full_name }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="card text-center py-8">
    <i class="fas fa-clipboard-list text-gray-300 text-4xl mb-4"></i>
    <h3 class="text-lg font-semibold text-gray-700 mb-2">No updates found</h3>
    <p class="text-gray-500">
        @if($selectedDate == today()->toDateString())
            No stock updates recorded today.
        @else
            No stock updates recorded on {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}.
        @endif
    </p>
</div>
@endif
</div>

<!-- Add Item Modal -->
<div id="addItemModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">Add New Item</h2>
                <button onclick="closeModal('addItemModal')" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('dashboard.inventory.items.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Item Name *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Current Stock *</label>
                            <input type="number" name="current_stock" required min="0" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Minimum Stock *</label>
                            <input type="number" name="minimum_stock" required min="0" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Unit *</label>
                        <select name="unit" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="pcs">Pieces</option>
                            <option value="sachets">Sachets</option>
                            <option value="bottles">Bottles</option>
                            <option value="tubes">Tubes</option>
                            <option value="ml">ML</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Category</label>
                        <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="hair_care">Hair Care</option>
                            <option value="skin_care">Skin Care</option>
                            <option value="nail_care">Nail Care</option>
                            <option value="tools">Tools</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="closeModal('addItemModal')" class="px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transition">
                        Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Stock Modal -->
<div id="updateStockModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">Update Stock</h2>
                <button onclick="closeModal('updateStockModal')" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form id="updateStockForm">
                @csrf
                <input type="hidden" id="item_id" name="item_id">
                
                <div id="itemInfo" class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <!-- Item info will be loaded here -->
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Update Type *</label>
                        <select name="type" id="type" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="">Select Type</option>
                            <option value="add">Add Stock (+)</option>
                            <option value="subtract">Use Stock (-)</option>
                            <option value="set">Set New Amount (=)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Quantity *</label>
                        <input type="number" name="quantity" id="quantity" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Remark *</label>
                        <textarea name="remark" id="remark" required rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="Describe what happened (e.g., 'Used 2 sachets for customer service')"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Required: Explain the stock change</p>
                    </div>
                    
                    <div id="lowStockWarning" class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg hidden">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                            <span class="text-yellow-800">This will make the item low on stock!</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="closeModal('updateStockModal')" class="px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition">
                        Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddItemModal() {
    document.getElementById('addItemModal').classList.remove('hidden');
}

function updateStockModal(itemId) {
    // Fetch item data
    fetch(`/dashboard/inventory/items/${itemId}`)
        .then(response => response.json())
        .then(item => {
            document.getElementById('item_id').value = item.id;
            document.getElementById('itemInfo').innerHTML = `
                <div class="font-medium text-gray-900">${item.name}</div>
                <div class="text-sm text-gray-600">
                    Current: ${item.current_stock} ${item.unit} • Min: ${item.minimum_stock}
                </div>
            `;
            document.getElementById('type').value = '';
            document.getElementById('quantity').value = '';
            document.getElementById('remark').value = '';
            document.getElementById('updateStockModal').classList.remove('hidden');
        });
}

// Real-time validation
document.getElementById('quantity').addEventListener('input', function() {
    const type = document.getElementById('type').value;
    const quantity = parseInt(this.value) || 0;
    const warningDiv = document.getElementById('lowStockWarning');
    
    if (!type) return;
    
    // This would calculate based on current stock
    // For simplicity, we'll just show warning for subtraction
    if (type === 'subtract' && quantity > 0) {
        warningDiv.classList.remove('hidden');
    } else {
        warningDiv.classList.add('hidden');
    }
});

// Form submission
document.getElementById('updateStockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const itemId = formData.get('item_id');
    
    fetch(`/dashboard/inventory/items/${itemId}/update-stock`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Stock updated successfully!', 'success');
            closeModal('updateStockModal');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert(data.message);
        }
    });
});

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
// Add showToast function to index.blade.php
function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    // Remove any existing toast
    const existingToast = document.querySelector('.custom-toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create new toast
    const toast = document.createElement('div');
    toast.className = `custom-toast fixed top-4 right-4 text-white px-4 py-2 rounded-lg shadow-lg z-50 ${colors[type] || colors.info}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
}
// Calendar Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const dateFilter = document.getElementById('dateFilter');
    
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            const selectedDate = this.value;
            if (selectedDate) {
                window.location.href = `?date=${selectedDate}`;
            }
        });
    }
});

function resetDateFilter() {
    window.location.href = '{{ route("dashboard.inventory.index") }}';
}

// Also update the showToast function to prevent duplicates
function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    // Remove any existing toast
    const existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(toast => toast.remove());
    
    // Create new toast
    const toast = document.createElement('div');
    toast.className = `custom-toast fixed top-4 right-4 text-white px-4 py-3 rounded-xl shadow-xl z-50 ${colors[type] || colors.info}`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
}
</script>
@endsection