@extends('layouts.dashboard')

@section('title', 'User Management - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Users</h1>
        <button onclick="openAddUserModal()" class="bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold py-3 px-6 rounded-xl shadow-lg transform hover:-translate-y-1 transition">
            <i class="fas fa-user-plus mr-2"></i> Add User
        </button>
    </div>
<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <!-- Admins -->
    <div class="card border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Admins</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">
                {{ $users->where('role', 'admin')->count() }}
            </p>
        </div>
    </div>

    <!-- Staff -->
    <div class="card border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-xl">
                    <i class="fas fa-users-cog text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Staff</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">
                {{ $users->where('role', 'staff')->count() }}
            </p>
        </div>
    </div>

    <!-- Customers -->
    <div class="card border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-xl">
                    <i class="fas fa-user-friends text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Customers</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">
                {{ $users->where('role', 'customer')->count() }}
            </p>
        </div>
    </div>

    <!-- Active Users -->
    <div class="card border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-xl">
                    <i class="fas fa-user-check text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Active Users</h3>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">
                {{ $users->where('is_active', true)->count() }}
            </p>
        </div>
    </div>
</div>
    <!-- Users Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">User</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Contact</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Role</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Last Login</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->full_name }}</div>
                                    <div class="text-sm text-gray-500">@{{ $user->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="text-gray-900">{{ $user->phone }}</div>
                            <div class="text-gray-500">{{ $user->email ?? 'No email' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 text-xs rounded-full font-semibold
                                {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $user->role == 'staff' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $user->role == 'customer' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 text-xs rounded-full font-semibold {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <form action="{{ route('dashboard.users.role', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                        <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer</option>
                                        <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                                <form action="{{ route('dashboard.users.toggle', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded-lg transition">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                            <p>No users found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">Add New User</h2>
                <button onclick="closeModal('addUserModal')" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Full Name</label>
                        <input type="text" name="full_name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Username</label>
                        <input type="text" name="username" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="Choose username">
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Phone</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="Phone number">
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Role</label>
                        <select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="customer">Customer</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="Enter password">
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none" placeholder="Confirm password">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="closeModal('addUserModal')" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-user-plus mr-2"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
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
@endsection