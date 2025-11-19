@extends('layouts.dashboard')

@section('title', 'Dashboard - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card border-l-4 border-blue-500">
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

        <div class="card border-l-4 border-green-500">
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

        <div class="card border-l-4 border-purple-500">
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

        <div class="card border-l-4 border-yellow-500">
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

    <!-- Quick Actions & Today's Appointments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Quick Actions -->
        <div class="card">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-bolt text-yellow-500 mr-2"></i> Quick Actions
            </h3>
            <div class="space-y-4">
                <button onclick="openBookingModal()" class="w-full bg-yellow-500 hover:bg-yellow-400 text-white font-bold py-4 rounded-xl transition transform hover:-translate-y-1 shadow-lg flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i> New Appointment
                </button>
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition transform hover:-translate-y-1 flex items-center justify-center">
                    <i class="fas fa-user-plus mr-2"></i> Walk-in Customer
                </button>
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="card">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-clock text-blue-500 mr-2"></i> Today's Appointments
            </h3>
            @if(isset($todayAppointments) && $todayAppointments->count() > 0)
                <div class="space-y-3">
                    @foreach($todayAppointments->take(5) as $appointment)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="text-center bg-blue-100 rounded-lg p-2 min-w-16">
                                <div class="font-bold text-blue-700">{{ $appointment->start_datetime->format('h:i') }}</div>
                                <div class="text-xs text-blue-600">{{ $appointment->start_datetime->format('A') }}</div>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $appointment->customer->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $appointment->service->name }}</div>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $appointment->status == 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $appointment->status == 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $appointment->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $appointment->status == 'completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No appointments scheduled for today.</p>
            @endif
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="card">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-calendar-alt text-purple-500 mr-2"></i> Upcoming Appointments (Next 7 Days)
        </h3>
        @if(isset($upcomingAppointments) && $upcomingAppointments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Date & Time</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Customer</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Service</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Staff</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($upcomingAppointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $appointment->start_datetime->format('M j, g:i A') }}
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
            <p class="text-gray-500 text-center py-4">No upcoming appointments in the next 7 days.</p>
        @endif
    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">New Appointment</h2>
                <button onclick="closeModal('bookingModal')" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('dashboard.appointments.store') }}" method="POST">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Customer</label>
                        <select name="customer_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="">Select Customer</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->full_name }} - {{ $customer->phone }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Service</label>
                        <select name="service_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="">Select Service</option>
                            @foreach($services ?? [] as $service)
                                <option value="{{ $service->id }}">{{ $service->name }} - ₱{{ number_format($service->price_regular) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Staff</label>
                        <select name="staff_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="">Select Staff</option>
                            @foreach($staff ?? [] as $staffMember)
                                <option value="{{ $staffMember->id }}">{{ $staffMember->user->full_name }} ({{ ucfirst($staffMember->specialty) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Date & Time</label>
                        <input type="datetime-local" name="start_datetime" required min="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="closeModal('bookingModal')" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-calendar-check mr-2"></i> Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openBookingModal() {
    document.getElementById('bookingModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('[id$="Modal"]');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
}
</script>
@endsection