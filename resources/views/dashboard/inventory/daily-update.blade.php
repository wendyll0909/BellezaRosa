<!-- [file name]: resources/views/dashboard/inventory/daily-update.blade.php -->
@extends('layouts.dashboard')

@section('title', 'Daily Stock Update - Belleza Rosa')

@section('content')

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Daily Stock Update</h1>
        <div class="flex space-x-3">
            <a href="{{ route('dashboard.inventory.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Inventory
            </a>
            <button onclick="saveAllUpdates()" 
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-xl shadow-lg transition">
                <i class="fas fa-save mr-2"></i> Save All Updates
            </button>
        </div>
    </div>

    <!-- Instructions -->
    <div class="card bg-blue-50">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-xl mr-4">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div>
                <h3 class="font-semibold text-blue-900">End-of-Day Process</h3>
                <p class="text-blue-700 text-sm">Update stock for all items used today. Add a remark for each update.</p>
            </div>
        </div>
    </div>

    <!-- Today's Date -->
    <div class="card">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900">{{ now()->format('l, F j, Y') }}</h2>
            <p class="text-gray-600">Record all stock changes made today</p>
        </div>
    </div>

    <!-- Update Form -->
    <div class="card">
        <form id="dailyUpdateForm">
            @csrf
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left">Item</th>
                            <th class="px-4 py-3 text-left">Current</th>
                            <th class="px-4 py-3 text-left">Update</th>
                            <th class="px-4 py-3 text-left">New Qty</th>
                            <th class="px-4 py-3 text-left">Remark *</th>
                            <th class="px-4 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="updateTableBody">
                        @foreach($items as $item)
                        @php
                            $todayUpdate = $todayUpdates[$item->id] ?? null;
                        @endphp
                        <tr id="row-{{ $item->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $item->name }}</div>
                                <div class="text-sm text-gray-500">{{ $item->unit }}</div>
                            </td>
                            
                            <td class="px-4 py-3">
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900">{{ $item->current_stock }}</div>
                                    <div class="text-xs text-gray-500">in stock</div>
                                </div>
                            </td>
                            
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <select name="items[{{ $item->id }}][type]" 
                                            class="update-type w-24 px-2 py-1 border border-gray-300 rounded-lg text-sm">
                                        <option value="">No Change</option>
                                        <option value="add" {{ $todayUpdate && $todayUpdate->type == 'add' ? 'selected' : '' }}>Add (+)</option>
                                        <option value="subtract" {{ $todayUpdate && $todayUpdate->type == 'subtract' ? 'selected' : '' }}>Use (-)</option>
                                    </select>
                                    
                                    <input type="number" 
                                           name="items[{{ $item->id }}][quantity]"
                                           class="update-quantity w-20 px-2 py-1 border border-gray-300 rounded-lg text-sm"
                                           min="1"
                                           value="{{ $todayUpdate ? $todayUpdate->quantity : '' }}"
                                           placeholder="Qty"
                                           {{ !$todayUpdate ? 'disabled' : '' }}>
                                </div>
                            </td>
                            
                            <td class="px-4 py-3">
                                <div class="text-center">
                                    <div id="new-qty-{{ $item->id }}" class="font-bold text-gray-900">
                                        {{ $item->current_stock }}
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-4 py-3">
                                <input type="text" 
                                       name="items[{{ $item->id }}][remark]"
                                       class="update-remark w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                       value="{{ $todayUpdate ? $todayUpdate->remark : '' }}"
                                       placeholder="e.g., Used for services"
                                       {{ !$todayUpdate ? 'disabled' : '' }}>
                            </td>
                            
                            <td class="px-4 py-3">
                                <button type="button" 
                                        onclick="removeItemFromUpdate({{ $item->id }})"
                                        class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition"
                                        title="Remove from update list">
                                    <i class="fas fa-times-circle"></i> Remove
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Hidden removed items input -->
            <input type="hidden" id="removedItems" name="removed_items" value="">
            
            <!-- Quick Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex space-x-3">
                    <button type="button" onclick="markAllUsed(1)" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm">
                        Mark All: Used 1 Today
                    </button>
                    <button type="button" onclick="clearAll()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm">
                        Clear All
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Removed Items Section (if any) -->
    <div id="removedItemsSection" class="card hidden">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">
            <i class="fas fa-eye-slash text-gray-500 mr-2"></i> Removed Items (Will not be updated)
        </h3>
        <div id="removedItemsList" class="space-y-2">
            <!-- Removed items will appear here -->
        </div>
        <button type="button" 
                onclick="restoreAllRemovedItems()"
                class="mt-3 px-3 py-1 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg">
            <i class="fas fa-redo-alt mr-1"></i> Restore All
        </button>
    </div>

    <!-- Save Button -->
    <div class="text-center">
        <button onclick="saveAllUpdates()" 
                class="w-full max-w-md bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-lg text-lg">
            <i class="fas fa-save mr-2"></i> Save All Daily Updates
        </button>
        <p class="text-sm text-gray-500 mt-2">Note: Removed items will not be updated</p>
    </div>
</div>
<!-- Remove Confirmation Modal -->
<div id="removeConfirmationModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">Remove Item</h2>
                <button onclick="closeModal('removeConfirmationModal')" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <p id="removeItemText" class="text-gray-700 mb-6">
                Are you sure you want to remove this item from the update list?
            </p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeModal('removeConfirmationModal')" 
                        class="px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button id="confirmRemoveBtn" 
                        onclick="confirmRemoveItem()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg transition">
                    <i class="fas fa-trash mr-2"></i> Remove
                </button>
            </div>
        </div>
    </div>
</div>
<script>
// ===== CORE FUNCTIONS =====
// Enhanced remove function with confirmation
let pendingRemoveItemId = null;

function removeItemFromUpdate(itemId) {
    const row = document.getElementById(`row-${itemId}`);
    if (!row) return;
    
    const itemName = row.querySelector('.font-medium').textContent;
    const currentStock = row.querySelector('.text-xl').textContent;
    const itemUnit = row.querySelector('.text-sm').textContent;
    
    // Set pending item and show modal
    pendingRemoveItemId = itemId;
    document.getElementById('removeItemText').innerHTML = `
        Are you sure you want to remove <strong>"${itemName}"</strong> from today's update list?<br><br>
        <span class="text-sm text-gray-600">
            • Current stock: ${currentStock} ${itemUnit}<br>
            • Item will not be updated today<br>
            • You can restore it later if needed
        </span>
    `;
    document.getElementById('removeConfirmationModal').classList.remove('hidden');
}

function confirmRemoveItem() {
    if (pendingRemoveItemId) {
        // Get item info
        const row = document.getElementById(`row-${pendingRemoveItemId}`);
        const itemName = row.querySelector('.font-medium').textContent;
        
        // Add to removed items array
        if (!removedItems.includes(pendingRemoveItemId)) {
            removedItems.push(pendingRemoveItemId);
        }
        
        // Update hidden input
        document.getElementById('removedItems').value = removedItems.join(',');
        
        // Move to removed items section
        row.remove();
        
        // Show removed items section
        const removedSection = document.getElementById('removedItemsSection');
        removedSection.classList.remove('hidden');
        
        // Add to removed items list
        const removedList = document.getElementById('removedItemsList');
        const removedItemDiv = document.createElement('div');
        removedItemDiv.className = 'flex items-center justify-between p-2 bg-gray-50 rounded-lg';
        removedItemDiv.id = `removed-${pendingRemoveItemId}`;
        removedItemDiv.innerHTML = `
            <div>
                <span class="font-medium text-gray-700">${itemName}</span>
                <span class="text-sm text-gray-500 ml-2">(${row.querySelector('.text-sm').textContent})</span>
                <div class="text-xs text-gray-500">Current: ${row.querySelector('.text-xl').textContent} in stock</div>
            </div>
            <button type="button" 
                    onclick="restoreItem(${pendingRemoveItemId})"
                    class="text-green-600 hover:text-green-800 hover:bg-green-50 p-1 rounded">
                <i class="fas fa-undo-alt"></i> Restore
            </button>
        `;
        removedList.appendChild(removedItemDiv);
        
        showToast(`"${itemName}" removed from update list`, 'warning');
        closeModal('removeConfirmationModal');
        pendingRemoveItemId = null;
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
// Store removed items
let removedItems = [];

// Check if showToast function exists, if not create a simple one
if (typeof showToast !== 'function') {
    function showToast(message, type = 'info') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500',
            warning: 'bg-yellow-500'
        };
        
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 text-white px-4 py-2 rounded-lg shadow-lg z-50 ${colors[type] || colors.info}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
}

// Remove item from update list
function removeItemFromUpdate(itemId) {
    const row = document.getElementById(`row-${itemId}`);
    if (!row) return;
    
    // Get item info before removing
    const itemName = row.querySelector('.font-medium').textContent;
    const itemUnit = row.querySelector('.text-sm').textContent;
    const currentStock = row.querySelector('.text-xl').textContent;
    
    // Add to removed items array
    if (!removedItems.includes(itemId)) {
        removedItems.push(itemId);
    }
    
    // Update hidden input
    document.getElementById('removedItems').value = removedItems.join(',');
    
    // Move to removed items section
    row.remove();
    
    // Show removed items section
    const removedSection = document.getElementById('removedItemsSection');
    removedSection.classList.remove('hidden');
    
    // Add to removed items list
    const removedList = document.getElementById('removedItemsList');
    const removedItemDiv = document.createElement('div');
    removedItemDiv.className = 'flex items-center justify-between p-2 bg-gray-50 rounded-lg';
    removedItemDiv.id = `removed-${itemId}`;
    removedItemDiv.innerHTML = `
        <div>
            <span class="font-medium text-gray-700">${itemName}</span>
            <span class="text-sm text-gray-500 ml-2">(${itemUnit})</span>
            <div class="text-xs text-gray-500">Current: ${currentStock} in stock</div>
        </div>
        <button type="button" 
                onclick="restoreItem(${itemId})"
                class="text-green-600 hover:text-green-800 hover:bg-green-50 p-1 rounded">
            <i class="fas fa-undo-alt"></i> Restore
        </button>
    `;
    removedList.appendChild(removedItemDiv);
    
    showToast(`"${itemName}" removed from update list`, 'warning');
}

// Restore a single item
function restoreItem(itemId) {
    // Remove from removed items array
    removedItems = removedItems.filter(id => id !== itemId);
    document.getElementById('removedItems').value = removedItems.join(',');
    
    // Remove from removed items list
    const removedItemDiv = document.getElementById(`removed-${itemId}`);
    if (removedItemDiv) {
        removedItemDiv.remove();
    }
    
    // Reload the row (we'll need to fetch it from the server)
    fetch(`/dashboard/inventory/items/${itemId}`)
        .then(response => response.json())
        .then(item => {
            // Recreate the row
            const tableBody = document.getElementById('updateTableBody');
            const newRow = document.createElement('tr');
            newRow.id = `row-${item.id}`;
            newRow.className = 'hover:bg-gray-50';
            newRow.innerHTML = `
                <td class="px-4 py-3">
                    <div class="font-medium text-gray-900">${item.name}</div>
                    <div class="text-sm text-gray-500">${item.unit}</div>
                </td>
                
                <td class="px-4 py-3">
                    <div class="text-center">
                        <div class="text-xl font-bold text-gray-900">${item.current_stock}</div>
                        <div class="text-xs text-gray-500">in stock</div>
                    </div>
                </td>
                
                <td class="px-4 py-3">
                    <div class="flex space-x-2">
                        <select name="items[${item.id}][type]" 
                                class="update-type w-24 px-2 py-1 border border-gray-300 rounded-lg text-sm">
                            <option value="">No Change</option>
                            <option value="add">Add (+)</option>
                            <option value="subtract">Use (-)</option>
                        </select>
                        
                        <input type="number" 
                               name="items[${item.id}][quantity]"
                               class="update-quantity w-20 px-2 py-1 border border-gray-300 rounded-lg text-sm"
                               min="1"
                               placeholder="Qty"
                               disabled>
                    </div>
                </td>
                
                <td class="px-4 py-3">
                    <div class="text-center">
                        <div id="new-qty-${item.id}" class="font-bold text-gray-900">
                            ${item.current_stock}
                        </div>
                    </div>
                </td>
                
                <td class="px-4 py-3">
                    <input type="text" 
                           name="items[${item.id}][remark]"
                           class="update-remark w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                           placeholder="e.g., Used for services"
                           disabled>
                </td>
                
                <td class="px-4 py-3">
                    <button type="button" 
                            onclick="removeItemFromUpdate(${item.id})"
                            class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition"
                            title="Remove from update list">
                        <i class="fas fa-times-circle"></i> Remove
                    </button>
                </td>
            `;
            
            tableBody.appendChild(newRow);
            
            // Reattach event listeners
            const typeSelect = newRow.querySelector('.update-type');
            typeSelect.addEventListener('change', function() {
                const row = this.closest('tr');
                const quantityInput = row.querySelector('.update-quantity');
                const remarkInput = row.querySelector('.update-remark');
                
                if (this.value) {
                    quantityInput.disabled = false;
                    remarkInput.disabled = false;
                    quantityInput.focus();
                } else {
                    quantityInput.disabled = true;
                    remarkInput.disabled = true;
                    quantityInput.value = '';
                    remarkInput.value = '';
                }
            });
            
            showToast(`"${item.name}" restored to update list`, 'success');
        });
    
    // Hide removed section if no items left
    if (removedItems.length === 0) {
        document.getElementById('removedItemsSection').classList.add('hidden');
    }
}

// Restore all removed items
function restoreAllRemovedItems() {
    const itemsToRestore = [...removedItems];
    itemsToRestore.forEach(itemId => {
        restoreItem(itemId);
    });
}

// Mark all as used
function markAllUsed(quantity = 1) {
    document.querySelectorAll('.update-type').forEach(select => {
        // Skip removed items
        const row = select.closest('tr');
        if (!row) return;
        
        select.value = 'subtract';
        select.dispatchEvent(new Event('change'));
        
        const quantityInput = row.querySelector('.update-quantity');
        const remarkInput = row.querySelector('.update-remark');
        
        quantityInput.value = quantity;
        remarkInput.value = 'Used for daily services';
    });
    
    showToast(`Marked all items as used ${quantity} unit(s) today`);
}

// Clear all updates (but keep items in list)
function clearAll() {
    if (!confirm('Clear all updates? This will reset all entries but keep items in the list.')) return;
    
    document.querySelectorAll('.update-type').forEach(select => {
        select.value = '';
        select.dispatchEvent(new Event('change'));
    });
    
    showToast('All updates cleared');
}

// Save all updates (excluding removed items)
function saveAllUpdates() {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const updates = [];
    const removedItemIds = removedItems; // Items removed from today's update
    
    // Get all rows that have updates (excluding removed items)
    document.querySelectorAll('tbody tr').forEach(row => {
        // Skip if this row is in removed items
        const itemId = row.id.replace('row-', '');
        if (removedItems.includes(parseInt(itemId))) return;
        
        const typeSelect = row.querySelector('.update-type');
        const quantityInput = row.querySelector('.update-quantity');
        const remarkInput = row.querySelector('.update-remark');
        
        if (typeSelect.value && quantityInput.value && remarkInput.value.trim()) {
            updates.push({
                item_id: itemId,
                type: typeSelect.value,
                quantity: parseInt(quantityInput.value),
                remark: remarkInput.value.trim()
            });
        }
    });
    
    console.log('Saving updates:', updates);
    console.log('Removed items to exclude:', removedItemIds);
    
    // Show loading indicator
    showToast(`Processing updates...`, 'info');
    
    // Send all data including removed items
    fetch(`/dashboard/inventory/daily-update-save`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            updates: updates,
            removed_items: removedItemIds
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Save response:', data);
        
        if (data.success) {
            showToast(`${data.saved_count} updates saved successfully!`, 'success');
            // Clear session storage
            sessionStorage.removeItem('dailyUpdateRemovedItems');
            setTimeout(() => {
                window.location.href = '{{ route("dashboard.inventory.index") }}';
            }, 1500);
        } else {
            alert('Save failed: ' + (data.message || 'Unknown error'));
            showToast('Save failed', 'error');
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        alert('Network error: ' + error.message);
        showToast('Network error', 'error');
    });
}

// ===== EVENT LISTENERS =====

// Enable/disable fields based on type
document.querySelectorAll('.update-type').forEach(select => {
    select.addEventListener('change', function() {
        const row = this.closest('tr');
        const quantityInput = row.querySelector('.update-quantity');
        const remarkInput = row.querySelector('.update-remark');
        
        if (this.value) {
            quantityInput.disabled = false;
            remarkInput.disabled = false;
            quantityInput.focus();
        } else {
            quantityInput.disabled = true;
            remarkInput.disabled = true;
            quantityInput.value = '';
            remarkInput.value = '';
        }
    });
});

// Initialize removed items from session storage if available
document.addEventListener('DOMContentLoaded', function() {
    const savedRemovedItems = sessionStorage.getItem('dailyUpdateRemovedItems');
    if (savedRemovedItems) {
        removedItems = JSON.parse(savedRemovedItems).map(id => parseInt(id));
        if (removedItems.length > 0) {
            // Remove items from table
            removedItems.forEach(itemId => {
                const row = document.getElementById(`row-${itemId}`);
                if (row) {
                    removeItemFromUpdate(itemId);
                }
            });
        }
    }
    
    // Save removed items to session storage before page unload
    window.addEventListener('beforeunload', function() {
        if (removedItems.length > 0) {
            sessionStorage.setItem('dailyUpdateRemovedItems', JSON.stringify(removedItems));
        } else {
            sessionStorage.removeItem('dailyUpdateRemovedItems');
        }
    });
});
</script>

@endsection