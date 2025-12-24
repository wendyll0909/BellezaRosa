@extends('layouts.dashboard')

@section('title', 'Appointments - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Appointments</h1>
        <button onclick="openBookingModal()" class="bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold py-3 px-6 rounded-xl shadow-lg transform hover:-translate-y-1 transition">
            <i class="fas fa-plus mr-2"></i> New Appointment
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="flex flex-col md:flex-row gap-4 md:gap-6">
        <!-- Total Appointments -->
        <div class="flex-1 card border-l-4 border-blue-500 min-w-0">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center min-w-0">
                    <div class="p-3 bg-blue-100 rounded-xl mr-3 flex-shrink-0">
                        <i class="fas fa-calendar-check text-blue-600 text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-medium text-gray-500 truncate">Total Appointments</h3>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 ml-2 flex-shrink-0">
                    {{ $totalAppointments ?? 0 }}
                </p>
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="flex-1 card border-l-4 border-green-500 min-w-0">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center min-w-0">
                    <div class="p-3 bg-green-100 rounded-xl mr-3 flex-shrink-0">
                        <i class="fas fa-calendar-day text-green-600 text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-medium text-gray-500 truncate">Today's Appointments</h3>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 ml-2 flex-shrink-0">
                    {{ $todayAppointments ?? 0 }}
                </p>
            </div>
        </div>

        <!-- Completed This Month -->
        <div class="flex-1 card border-l-4 border-red-500 min-w-0">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center min-w-0">
                    <div class="p-3 bg-red-100 rounded-xl mr-3 flex-shrink-0">
                        <i class="fas fa-check-circle text-red-600 text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-medium text-gray-500 truncate">Completed (Month)</h3>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 ml-2 flex-shrink-0">
                    {{ $completedThisMonth ?? 0 }}
                </p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <form action="{{ route('dashboard.appointments.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col md:flex-row gap-4 flex-1">
                <!-- Search Input -->
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Search by name, phone, or service..." 
                    class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none w-full md:w-64">
                
                <!-- Status Filter -->
                <select 
                    name="status" 
                    class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                
                <!-- Date Filter -->
                <input 
                    type="date" 
                    name="date" 
                    value="{{ request('date') }}"
                    class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
            </div>
            
            <!-- Filter and Clear Buttons -->
            <div class="flex gap-2">
                <button 
                    type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl transition">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
                
                @if(request()->hasAny(['search', 'status', 'date']))
                <a 
                    href="{{ route('dashboard.appointments.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-xl transition">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold">Date & Time</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Service</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Staff</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Amount</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-gray-900">{{ $appointment->start_datetime->format('M j, Y') }}</div>
                            <div class="text-gray-500">{{ $appointment->start_datetime->format('g:i A') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $appointment->customer->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $appointment->customer->phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $appointment->service->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $appointment->staff->user->full_name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">₱{{ number_format($appointment->total_amount, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold
                                {{ $appointment->status == 'scheduled' ? 'text-blue-600' : '' }}
                                {{ $appointment->status == 'confirmed' ? 'text-green-600' : '' }}
                                {{ $appointment->status == 'in_progress' ? 'text-yellow-600' : '' }}
                                {{ $appointment->status == 'completed' ? 'text-gray-600' : '' }}
                                {{ $appointment->status == 'cancelled' ? 'text-red-600' : '' }}">
                                {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                            </span>
                        </td>

                        <!-- Actions Column -->
                        <td class="px-4 py-3">
                            <div class="flex space-x-3 items-center">

                                <!-- Status Update -->
                                <form action="{{ route('dashboard.appointments.status', $appointment) }}" method="POST" class="inline">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()"
                                        class="text-xs font-semibold rounded-lg px-3 py-1 bg-blue-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-300">
                                        <option value="scheduled" {{ $appointment->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="in_progress" {{ $appointment->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>

                                <!-- Edit -->
                                <a href="{{ route('dashboard.appointments.edit', $appointment) }}"
                                   class="text-blue-600 hover:text-blue-800 transition" title="Edit Appointment">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Cancel Appointment Button (only if not already cancelled) -->
                                @if(!$appointment->isCancelled())
                                    <a href="{{ route('dashboard.appointments.cancel.form', $appointment) }}"
                                       class="text-red-600 hover:text-red-800 transition" 
                                       title="Cancel Appointment"
                                       onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                        <i class="fas fa-times-circle text-lg"></i>
                                    </a>
                                @endif

                                <!-- Record Payment -->
                                <a href="{{ route('dashboard.payments.create', $appointment) }}"
                                   class="text-green-600 hover:text-green-800 transition"
                                   title="Record Payment">
                                    <i class="fas fa-money-bill-wave"></i>
                                </a>

                                <!-- View Payment (if exists) -->
                                @if($appointment->payment()->exists())
                                    <a href="{{ route('dashboard.payments.show', $appointment->payment) }}"
                                       class="text-indigo-600 hover:text-indigo-800 transition"
                                       title="View Payment Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif

                                <!-- Paid Indicator -->
                                @if($appointment->payment && $appointment->payment->isPaid())
                                    <span class="text-green-600 font-bold" title="Fully Paid">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300"></i>
                            <p>No appointments found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($appointments->hasPages())
        <div class="mt-6">
            {{ $appointments->links() }}
        </div>
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
                      data-toast="true"
      data-toast-message="Appointment created successfully!"
      data-toast-type="success">
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
                    <!-- Date & Time Input -->
<div class="form-group">
    <label class="block text-gray-700 font-semibold mb-2">Date & Time</label>
    <input 
        type="datetime-local" 
        name="start_datetime" 
        required 
        min="{{ now()->format('Y-m-d\T') . substr($openingTime, 0, 5) }}"
        max="{{ now()->addDays($maxDaysAhead)->format('Y-m-d\T') . substr($closingTime, 0, 5) }}"
        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none appointment-time"
        step="{{ $slotInterval * 60 }}"
        onchange="validateAppointmentTime(this)"
    >
    <p class="text-sm text-gray-500 mt-1">
        Business hours: {{ \Carbon\Carbon::createFromFormat('H:i:s', $openingTime)->format('g:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $closingTime)->format('g:i A') }}
    </p>
    <p class="text-xs text-red-600 mt-1 hidden" id="timeError">
        Please select a time within business hours.
    </p>
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
// Function to validate appointment time
function validateAppointmentTime(input) {
    if (!input.value) return true;
    
    const selectedDateTime = new Date(input.value);
    const selectedTime = selectedDateTime.toTimeString().split(' ')[0].substring(0, 5); // HH:mm format
    
    // Parse business hours (passed from PHP)
    const openingTime = '{{ substr($openingTime, 0, 5) }}'; // e.g., "09:00"
    const closingTime = '{{ substr($closingTime, 0, 5) }}'; // e.g., "20:00"
    
    const errorElement = document.getElementById('timeError');
    
    // Check if selected time is within business hours
    if (selectedTime < openingTime || selectedTime > closingTime) {
        errorElement.textContent = `Please select a time within business hours (${openingTime} - ${closingTime})`;
        errorElement.classList.remove('hidden');
        input.classList.add('border-red-500');
        input.classList.remove('border-gray-300');
        return false;
    } else {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
}

// Enhanced openBookingModal function with time constraints
function openBookingModal() {
    const modal = document.getElementById('bookingModal');
    modal.classList.remove('hidden');
    
    // Get current date/time
    const now = new Date();
    const today = now.toISOString().split('T')[0];
    const currentTime = now.toTimeString().split(' ')[0].substring(0, 5); // HH:mm
    
    // Salon settings from PHP
    const openingTime = '{{ substr($openingTime, 0, 5) }}';
    const closingTime = '{{ substr($closingTime, 0, 5) }}';
    const maxDaysAhead = {{ $maxDaysAhead }};
    const slotInterval = {{ $slotInterval }};
    
    // Calculate max date
    const maxDate = new Date(now);
    maxDate.setDate(maxDate.getDate() + maxDaysAhead);
    const maxDateStr = maxDate.toISOString().split('T')[0];
    
    // Set min time: if current time is before opening, use opening time
    let minTime = openingTime;
    if (currentTime > openingTime && currentTime < closingTime) {
        // Round current time to nearest slot interval
        const [currentHour, currentMinute] = currentTime.split(':').map(Number);
        const roundedMinutes = Math.ceil(currentMinute / slotInterval) * slotInterval;
        
        let roundedHour = currentHour;
        let roundedMin = roundedMinutes;
        
        if (roundedMinutes >= 60) {
            roundedHour = currentHour + 1;
            roundedMin = 0;
        }
        
        minTime = `${roundedHour.toString().padStart(2, '0')}:${roundedMin.toString().padStart(2, '0')}`;
    }
    
    // If minTime is after closing time, set to next day's opening
    if (minTime >= closingTime) {
        // Move to next day
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        minTime = openingTime;
        
        // Update all datetime inputs in the modal
        const dateTimeInputs = document.querySelectorAll('.appointment-time');
        dateTimeInputs.forEach(input => {
            input.min = `${tomorrowStr}T${minTime}`;
            input.max = `${maxDateStr}T${closingTime}`;
            input.step = slotInterval * 60; // Convert minutes to seconds
        });
    } else {
        // Update all datetime inputs in the modal
        const dateTimeInputs = document.querySelectorAll('.appointment-time');
        dateTimeInputs.forEach(input => {
            input.min = `${today}T${minTime}`;
            input.max = `${maxDateStr}T${closingTime}`;
            input.step = slotInterval * 60; // Convert minutes to seconds
        });
    }
    
    // Clear any previous errors
    const errorElement = document.getElementById('timeError');
    if (errorElement) {
        errorElement.classList.add('hidden');
    }
}

// Override form submission to validate time
document.addEventListener('DOMContentLoaded', function() {
    const appointmentForm = document.querySelector('form[action="{{ route("dashboard.appointments.store") }}"]');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', function(e) {
            const dateTimeInput = document.querySelector('.appointment-time');
            if (dateTimeInput && !validateAppointmentTime(dateTimeInput)) {
                e.preventDefault();
                const openingTime = '{{ substr($openingTime, 0, 5) }}';
                const closingTime = '{{ substr($closingTime, 0, 5) }}';
                alert(`Please select a time within business hours: ${openingTime} - ${closingTime}`);
                dateTimeInput.focus();
            }
        });
    }
});

// Keep your existing closeModal and window.onclick functions
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