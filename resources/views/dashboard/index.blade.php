@extends('layouts.dashboard')

@section('title', 'Dashboard - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header with Date Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Overview</h1>
        <div class="flex flex-col sm:flex-row gap-3">
            <select id="dateFilter" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                <option value="today" {{ ($currentFilter ?? 'today') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_week" {{ ($currentFilter ?? '') == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_month" {{ ($currentFilter ?? '') == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="custom" {{ ($currentFilter ?? '') == 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
            <div id="customDateRange" class="hidden sm:flex gap-2">
                <input type="month" id="customMonth" value="{{ $currentCustomDate ?? '' }}" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                <button onclick="applyCustomDate()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition">Apply</button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500" id="appointmentsLabel">{{ $stats_label ?? "Today's" }} Appointments</h3>
                    <p class="text-2xl font-bold text-gray-900" id="appointmentsCount">{{ $appointments_count ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="card border-l-4 border-green-500 cursor-pointer" onclick="viewCustomerServices()">
    <div class="flex items-center">
        <div class="p-3 bg-green-100 rounded-xl">
            <i class="fas fa-users text-green-600 text-xl"></i>
        </div>
        <div class="ml-4">
            <h3 class="text-sm font-medium text-gray-500" id="customersLabel">
                {{ $stats_label ?? "Today's" }} Total Customers
            </h3>
            <p class="text-2xl font-bold text-gray-900" id="totalCustomers">{{ $total_customers ?? 0 }}</p>
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
                    <p class="text-2xl font-bold text-gray-900" id="totalStaff">{{ $total_staff ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="card border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-xl">
                    <i class="fas fa-money-bill-wave text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500" id="revenueLabel">{{ $stats_label ?? "Today's" }} Revenue</h3>
                    <p class="text-2xl font-bold text-gray-900" id="revenueAmount">₱{{ number_format($revenue ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Appointments -->
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
                <a href="{{ route('dashboard.appointments.index') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition transform hover:-translate-y-1 flex items-center justify-center">
                    <i class="fas fa-list mr-2"></i> View All Appointments
                </a>
            </div>
        </div>

        <!-- Range Appointments -->
        <div class="card">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-clock text-blue-500 mr-2"></i> 
                <span id="appointmentsSectionLabel">{{ $stats_label ?? "Today's" }} Appointments</span>
            </h3>
            <div id="appointmentsList">
                @if(isset($rangeAppointments) && $rangeAppointments->count() > 0)
                    <div class="space-y-3">
                        @foreach($rangeAppointments->take(5) as $appointment)
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
                    <p class="text-gray-500 text-center py-4">No appointments for selected period.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="card">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-calendar-alt text-purple-500 mr-2"></i> Upcoming Appointments (Next 7 Days)
        </h3>
        <div id="upcomingAppointmentsList">
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
</div>

<!-- Include the same modals as before -->
@include('dashboard.partials.customer-services-modal')
@include('dashboard.partials.booking-modal')

<script>
// Date Filter Functionality
document.getElementById('dateFilter').addEventListener('change', function() {
    const customRange = document.getElementById('customDateRange');
    if (this.value === 'custom') {
        customRange.classList.remove('hidden');
    } else {
        customRange.classList.add('hidden');
        applyDateFilter(this.value);
    }
});

function applyDateFilter(filter) {
    showLoading();
    
    fetch('{{ route("dashboard.filter") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            date_range: filter
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDashboard(data);
            showToast(`Filter applied: ${filter.replace('_', ' ')}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error applying filter', 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

function applyCustomDate() {
    const month = document.getElementById('customMonth').value;
    if (month) {
        showLoading();
        
        fetch('{{ route("dashboard.filter") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                date_range: 'custom',
                custom_date: month
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDashboard(data);
                showToast(`Custom filter applied: ${month}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error applying filter', 'error');
        })
        .finally(() => {
            hideLoading();
        });
    } else {
        showToast('Please select a month', 'error');
    }
}

function updateDashboard(data) {
    // Update statistics
    document.getElementById('appointmentsLabel').textContent = data.label + ' Appointments';
    document.getElementById('appointmentsCount').textContent = data.stats.appointments_count;
    document.getElementById('revenueLabel').textContent = data.label + ' Revenue';
    document.getElementById('revenueAmount').textContent = '₱' + Number(data.stats.revenue).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    document.getElementById('totalCustomers').textContent = data.stats.customers_count;
    document.getElementById('totalStaff').textContent = data.stats.total_staff;
    
    // Store the current filter data for use in customer services modal
    window.currentFilterData = data;
    
    // Update appointments section label
    document.getElementById('appointmentsSectionLabel').textContent = data.label + ' Appointments';
    
    // Update appointments list
    const appointmentsList = document.getElementById('appointmentsList');
    if (data.appointments && data.appointments.length > 0) {
        let appointmentsHtml = '<div class="space-y-3">';
        data.appointments.slice(0, 5).forEach(appointment => {
            const startTime = new Date(appointment.start_datetime);
            const timeString = startTime.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            
            appointmentsHtml += `
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="text-center bg-blue-100 rounded-lg p-2 min-w-16">
                            <div class="font-bold text-blue-700">${timeString.replace(':00', '')}</div>
                            <div class="text-xs text-blue-600">${startTime.getHours() >= 12 ? 'PM' : 'AM'}</div>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">${appointment.customer.full_name}</div>
                            <div class="text-sm text-gray-500">${appointment.service.name}</div>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full ${getStatusClass(appointment.status)}">
                        ${appointment.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                    </span>
                </div>
            `;
        });
        appointmentsHtml += '</div>';
        appointmentsList.innerHTML = appointmentsHtml;
    } else {
        appointmentsList.innerHTML = '<p class="text-gray-500 text-center py-4">No appointments for selected period.</p>';
    }
// 🔥 ADD THIS LINE - Update customers label dynamically
document.getElementById('customersLabel').textContent = data.label + ' Total Customers';    
}
// Customer Services Modal Functions
function viewCustomerServices() {
    // Use the current filter data if available
    if (window.currentFilterData) {
        updateCustomerServicesModal(window.currentFilterData);
    } else {
        // If no filter data, use the initial page data
        updateCustomerServicesModal({
            customer_services: {
                customers: @json($customersWithServices),
                total_services: {{ $totalServices }},
                popular_service: '{{ $popularService }}'
            },
            label: '{{ $stats_label ?? "Today\'s" }}',
            stats: {
                customers_count: {{ $total_customers ?? 0 }}
            }
        });
    }
    document.getElementById('customerServicesModal').classList.remove('hidden');
}

function updateCustomerServicesModal(data) {
    const modal = document.getElementById('customerServicesModal');
    const label = data.label || 'Selected Period';
    
    // Update modal title with current filter
    const modalTitle = modal.querySelector('h2');
    modalTitle.textContent = `Customer Services Report - ${label}`;
    
    // Update statistics in modal
    const totalCustomersElem = modal.querySelector('.bg-blue-50 .text-2xl');
    const totalServicesElem = modal.querySelector('.bg-green-50 .text-2xl');
    const popularServiceElem = modal.querySelector('.bg-purple-50 .text-2xl');
    
    if (totalCustomersElem) {
        totalCustomersElem.textContent = data.stats.customers_count || 0;
    }
    
    if (totalServicesElem) {
        totalServicesElem.textContent = data.customer_services.total_services || 0;
    }
    
    if (popularServiceElem) {
        popularServiceElem.textContent = data.customer_services.popular_service || 'N/A';
    }
    
    // Update customers table
    const tableBody = modal.querySelector('tbody');
    if (data.customer_services.customers && data.customer_services.customers.length > 0) {
        let tableHtml = '';
        data.customer_services.customers.forEach(customer => {
            tableHtml += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">${customer.full_name}</div>
                        <div class="text-sm text-gray-500">${customer.phone}</div>
                    </td>
                    <td class="px-4 py-3">
            `;
            
            if (customer.appointments && customer.appointments.length > 0) {
                tableHtml += '<div class="space-y-1">';
                customer.appointments.slice(0, 3).forEach(appointment => {
                    const appointmentDate = new Date(appointment.start_datetime);
                    tableHtml += `
                        <div class="text-sm text-gray-700">
                            • ${appointment.service.name}
                            <span class="text-xs text-gray-500">(${appointmentDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })})</span>
                        </div>
                    `;
                });
                if (customer.appointments.length > 3) {
                    tableHtml += `<div class="text-xs text-blue-600">+${customer.appointments.length - 3} more services</div>`;
                }
                tableHtml += '</div>';
            } else {
                tableHtml += '<span class="text-gray-400 text-sm">No services availed in this period</span>';
            }
            
            tableHtml += `
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        ${customer.range_appointments_count || customer.appointments_count || 0}
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-green-600">
                        ₱${(customer.total_spent || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}
                    </td>
                </tr>
            `;
        });
        tableBody.innerHTML = tableHtml;
    } else {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                    <p>No customers with services in the selected period.</p>
                </td>
            </tr>
        `;
    }
}

function getStatusClass(status) {
    const statusClasses = {
        'scheduled': 'bg-blue-100 text-blue-800',
        'confirmed': 'bg-green-100 text-green-800',
        'in_progress': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-gray-100 text-gray-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
}

function showLoading() {
    // You can add a loading spinner here
    console.log('Loading...');
}

function hideLoading() {
    // Hide loading spinner
    console.log('Loading complete');
}

// Toast Notification
function showToast(message, type = 'success') {
    // Create toast element if it doesn't exist
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-y-10 opacity-0';
        document.body.appendChild(toast);
    }

    // Set toast content and style
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;

    // Show toast
    setTimeout(() => {
        toast.classList.remove('translate-y-10', 'opacity-0');
    }, 100);

    // Hide toast after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
    }, 3000);
}

// Initialize custom date range visibility
document.addEventListener('DOMContentLoaded', function() {
    const dateFilter = document.getElementById('dateFilter');
    const customRange = document.getElementById('customDateRange');
    
    if (dateFilter.value === 'custom') {
        customRange.classList.remove('hidden');
    }
});
// Close modal when clicking outside or ESC key
function setupModalClosing() {
    // Click outside to close
    window.onclick = function(event) {
        const modals = document.querySelectorAll('[id$="Modal"]');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }

    // ESC key to close
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('[id$="Modal"]');
            modals.forEach(modal => {
                modal.classList.add('hidden');
            });
        }
    });
}

// Initialize modal closing when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupModalClosing();
    
    // Your existing initialization code should be here...
    const dateFilter = document.getElementById('dateFilter');
    const customRange = document.getElementById('customDateRange');
    
    if (dateFilter.value === 'custom') {
        customRange.classList.remove('hidden');
    }
});
</script>
@endsection