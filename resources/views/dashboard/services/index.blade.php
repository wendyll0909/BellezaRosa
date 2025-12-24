@extends('layouts.dashboard')

@section('title', 'Services - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Services</h1>
        <button onclick="openAddServiceModal()" class="bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold py-3 px-6 rounded-xl shadow-lg transform hover:-translate-y-1 transition">
            <i class="fas fa-plus mr-2"></i> Add Service
        </button>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($services as $service)
        <div class="card hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold text-gray-900">{{ $service->name }}</h3>
                <span class="px-2 py-1 text-xs rounded-full {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            
            <p class="text-gray-600 mb-4 line-clamp-2">{{ $service->description ?? 'No description available.' }}</p>
            
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Duration:</span>
                    <span class="font-semibold">{{ $service->duration_minutes }} minutes</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Regular Price:</span>
                    <span class="font-semibold text-green-600">₱{{ number_format($service->price_regular, 2) }}</span>
                </div>
                @if($service->price_premium)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Premium Price:</span>
                    <span class="font-semibold text-purple-600">₱{{ number_format($service->price_premium, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Category:</span>
                    <span class="font-semibold">{{ $service->category->name ?? 'Uncategorized' }}</span>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="flex space-x-2 pt-4 border-t border-gray-200">
                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition text-sm font-semibold">
                    <i class="fas fa-edit mr-1"></i> Edit
                </button>
                <form action="{{ route('dashboard.services.destroy', $service) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this service?')" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition text-sm font-semibold">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full">
            <div class="card text-center py-12">
                <i class="fas fa-spa text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-500 mb-2">No Services Found</h3>
                <p class="text-gray-400 mb-4">Get started by adding your first service.</p>
                <button onclick="openAddServiceModal()" class="bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold py-2 px-6 rounded-xl transition">
                    Add Service
                </button>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Add Service Modal -->
<div id="addServiceModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">Add New Service</h2>
                <button onclick="closeModal('addServiceModal')" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('dashboard.services.store') }}" method="POST">
                      data-toast="true"
      data-toast-message="Service created successfully!"
      data-toast-type="success">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Service Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="e.g., Gel Manicure">
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Category</label>
                        <select name="category_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="">Select Category</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" required value="60" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Regular Price</label>
                        <input type="number" step="0.01" name="price_regular" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Premium Price (Optional)</label>
                        <input type="number" step="0.01" name="price_premium" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="0.00">
                    </div>
                    <div class="form-group flex items-center">
                        <input type="checkbox" name="is_premium" id="is_premium" class="mr-2 rounded">
                        <label for="is_premium" class="text-gray-700 font-semibold">Premium Service</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="Service description..."></textarea>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="closeModal('addServiceModal')" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-plus mr-2"></i> Add Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddServiceModal() {
    document.getElementById('addServiceModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection