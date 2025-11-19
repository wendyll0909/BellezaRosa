@extends('layouts.dashboard')
@section('title', 'Manage Users')

@section('content')
<div class="bg-white rounded-2xl shadow-lg p-8">
    <h2 class="text-3xl font-bold text-blue-900 mb-6">User Management</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="p-4 text-left">Name</th>
                    <th class="p-4 text-left">Username</th>
                    <th class="p-4 text-left">Phone</th>
                    <th class="p-4 text-left">Current Role</th>
                    <th class="p-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\User::all() as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4">{{ $user->full_name }}</td>
                    <td class="p-4">{{ $user->username }}</td>
                    <td class="p-4">{{ $user->phone }}</td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-white {{ $user->role == 'admin' ? 'bg-purple-600' : ($user->role == 'staff' ? 'bg-blue-600' : 'bg-green-600') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="p-4">
                        <form action="{{ route('dashboard.users.role', $user) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <select name="role" onchange="this.form.submit()" class="px-3 py-1 border rounded-lg">
                                <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection