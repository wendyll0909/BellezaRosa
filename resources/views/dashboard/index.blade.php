<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Belleza Rosa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { 
            --primary: #1E40AF; 
            --gold: #F59E0B; 
            --light: #F8FAFC; 
        }
        .sidebar { 
            width: 260px; 
            background: var(--primary); 
            color: white; 
            position: fixed; 
            height: 100%; 
            overflow-y: auto; 
        }
        .nav-item { 
            padding: 16px 30px; 
            display: flex; 
            align-items: center; 
            cursor: pointer; 
            transition: all 0.3s ease;
        }
        .nav-item:hover, .nav-item.active { 
            background: #1E3A8A; 
        }
        .nav-item.active { 
            border-left: 5px solid var(--gold); 
            background: #3B82F6; 
            font-weight: 600; 
        }
        .main-content { 
            margin-left: 260px; 
            padding: 25px; 
            min-height: 100vh;
            background: #F8FAFC;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="p-6 text-center border-b border-white border-opacity-20">
            <h1 class="text-2xl font-bold flex items-center justify-center">
                <i class="fas fa-spa text-yellow-500 mr-2"></i> Belleza Rosa
            </h1>
            <p class="text-sm opacity-75 mt-2">Welcome, {{ auth()->user()->full_name }}</p>
        </div>
        <nav class="mt-6">
            <div class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.index') }}'">
                <i class="fas fa-home mr-3"></i> Dashboard
            </div>
            <div class="nav-item {{ request()->is('dashboard/appointments*') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.appointments.index') }}'">
                <i class="fas fa-calendar-check mr-3"></i> Appointments
            </div>
            <div class="nav-item {{ request()->is('dashboard/services*') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.services.index') }}'">
                <i class="fas fa-spa mr-3"></i> Services
            </div>
            @if(auth()->user()->isAdmin())
                <div class="nav-item {{ request()->is('dashboard/users*') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.users.index') }}'">
                    <i class="fas fa-users-cog mr-3"></i> Manage Users
                </div>
            @endif
            <div class="nav-item mt-auto" onclick="document.getElementById('logout-form').submit()">
                <i class="fas fa-sign-out-alt mr-3"></i> Logout
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="space-y-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-xl">
                            <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Today's Appointments</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['today_appointments'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-xl">
                            <i class="fas fa-users text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Customers</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_customers'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-xl">
                            <i class="fas fa-user-tie text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Staff Members</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_staff'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-xl">
                            <i class="fas fa-money-bill-wave text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Today's Revenue</h3>
                            <p class="text-2xl font-bold text-gray-900">₱{{ number_format($stats['revenue_today'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Appointments -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Today's Appointments</h3>
                @if(isset($todayAppointments) && $todayAppointments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Time</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Customer</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Service</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Staff</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($todayAppointments as $appointment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $appointment->start_datetime->format('g:i A') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $appointment->customer->full_name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $appointment->service->name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $appointment->staff->user->full_name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $appointment->status == 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $appointment->status == 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $appointment->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $appointment->status == 'completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No appointments scheduled for today.</p>
                @endif
            </div>

            <!-- Upcoming Appointments -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Upcoming Appointments (Next 7 Days)</h3>
                @if(isset($upcomingAppointments) && $upcomingAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingAppointments as $appointment)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $appointment->start_datetime->format('M j') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $appointment->start_datetime->format('D') }}
                                    </div>
                                </div>
                                <div class="w-px h-8 bg-gray-300"></div>
                                <div>
                                    <div class="font-medium text-gray-900">
                                        {{ $appointment->customer->full_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $appointment->service->name }} with {{ $appointment->staff->user->full_name ?? 'Unassigned' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-gray-900">
                                    {{ $appointment->start_datetime->format('g:i A') }}
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full 
                                    {{ $appointment->status == 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $appointment->status == 'confirmed' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No upcoming appointments in the next 7 days.</p>
                @endif
            </div>
        </div>
    </main>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>
</body>
</html>