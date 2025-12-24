@extends('layouts.dashboard')

@section('title', 'Staff Dashboard - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Staff Dashboard</h1>
        <div class="flex items-center space-x-4">
            <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-xl font-medium">
                <i class="fas fa-user-tie mr-2"></i>
                {{ auth()->user()->full_name }}
            </div>
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded-xl font-medium">
                <i class="fas fa-star mr-2"></i>
                {{ ucfirst(auth()->user()->staff->specialty) }} Specialist
            </div>
        </div>
    </div>

    <!-- Staff Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Appointments -->
        <div class="card border-l-4 border-blue-500">
            <div class="flex">
                <div class="p-3 bg-blue-100 rounded-xl h-fit">
                    <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4 flex-1 flex flex-col justify-between">
                    <h3 class="text-sm font-medium text-gray-500">Today's Appointments</h3>
                    <p class="text-2xl font-bold text-gray-900" id="todayAppointments">
                        {{ $todayAppointments ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="card border-l-4 border-green-500">
            <div class="flex">
                <div class="p-3 bg-green-100 rounded-xl h-fit">
                    <i class="fas fa-calendar-alt text-green-600 text-xl"></i>
                </div>
                <div class="ml-4 flex-1 flex flex-col justify-between">
                    <h3 class="text-sm font-medium text-gray-500">Upcoming (7 days)</h3>
                    <p class="text-2xl font-bold text-gray-900" id="upcomingAppointments">
                        {{ $upcomingAppointmentsCount ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Completed This Month -->
        <div class="card border-l-4 border-purple-500">
            <div class="flex">
                <div class="p-3 bg-purple-100 rounded-xl h-fit">
                    <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4 flex-1 flex flex-col justify-between">
                    <h3 class="text-sm font-medium text-gray-500">Completed (Month)</h3>
                    <p class="text-2xl font-bold text-gray-900" id="completedThisMonth">
                        {{ $completedThisMonth ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Commission Earned -->
        <div class="card border-l-4 border-yellow-500">
            <div class="flex">
                <div class="p-3 bg-yellow-100 rounded-xl h-fit">
                    <i class="fas fa-money-bill-wave text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4 flex-1 flex flex-col justify-between">
                    <h3 class="text-sm font-medium text-gray-500">Commission (Month)</h3>
                    <p class="text-2xl font-bold text-gray-900" id="monthlyCommission">
                        ₱{{ number_format($monthlyCommission ?? 0, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="card">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-clock text-blue-500 mr-2"></i> Today's Schedule
        </h3>
        
        @if($todaySchedule->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Time</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Customer</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Service</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($todaySchedule as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <div class="font-medium">{{ $appointment->start_datetime->format('g:i A') }}</div>
                                <div class="text-xs text-gray-500">{{ $appointment->duration }} mins</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $appointment->customer->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $appointment->customer->phone }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $appointment->service->name }}
                                <div class="text-xs text-gray-500">₱{{ number_format($appointment->total_amount, 2) }}</div>
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
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    @if($appointment->status == 'scheduled' || $appointment->status == 'confirmed')
                                        <form action="{{ route('dashboard.appointments.status', $appointment) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="Start Service">
                                                <i class="fas fa-play-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($appointment->status == 'in_progress')
                                        <form action="{{ route('dashboard.appointments.status', $appointment) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="text-green-600 hover:text-green-800" title="Mark as Completed">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('dashboard.appointments.show', $appointment) }}" 
                                       class="text-blue-600 hover:text-blue-800" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No appointments scheduled for today</p>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Service Status Update -->
        <div class="card">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-tasks text-green-500 mr-2"></i> Quick Actions
            </h3>
            <div class="space-y-4">
                <a href="{{ route('dashboard.appointments.index') }}" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition transform hover:-translate-y-1 flex items-center justify-center">
                    <i class="fas fa-list mr-2"></i> View All Appointments
                </a>
                
                <button onclick="openServiceReportModal()" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition transform hover:-translate-y-1 flex items-center justify-center">
                    <i class="fas fa-file-alt mr-2"></i> Daily Service Report
                </button>
                
                <a href="{{ route('staff.commission') }}" 
                   class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition transform hover:-translate-y-1 flex items-center justify-center">
                    <i class="fas fa-chart-line mr-2"></i> View Commission
                </a>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="card">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-calendar-plus text-purple-500 mr-2"></i> Upcoming Appointments
            </h3>
            
            @if($upcomingAppointments->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingAppointments as $appointment)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="text-center bg-blue-100 rounded-lg p-2 min-w-16">
                                    <div class="font-bold text-blue-700">{{ $appointment->start_datetime->format('M j') }}</div>
                                    <div class="text-xs text-blue-600">{{ $appointment->start_datetime->format('D') }}</div>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $appointment->customer->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $appointment->service->name }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-gray-900">{{ $appointment->start_datetime->format('g:i A') }}</div>
                                <span class="text-xs font-medium
                                    {{ $appointment->status == 'scheduled' ? 'text-blue-700' : '' }}
                                    {{ $appointment->status == 'confirmed' ? 'text-green-700' : '' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($upcomingAppointments->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('dashboard.appointments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All Upcoming Appointments <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <p class="text-gray-500">No upcoming appointments</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Service Report Modal -->
<div id="serviceReportModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="bg-gradient-to-r from-green-900 to-green-700 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">Daily Service Report</h2>
                <button onclick="closeServiceReportModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form id="serviceReportForm" action="{{ route('staff.service-report') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Date *</label>
                        <input type="date" name="report_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-600 outline-none">
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Services Completed *</label>
                        <div id="servicesContainer">
                            @foreach($todaySchedule as $appointment)
                                @if($appointment->status == 'completed')
                                <div class="mb-3 p-3 border border-gray-200 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="font-medium">{{ $appointment->service->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $appointment->customer->full_name }}</div>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <div>
                                            <span class="font-medium">Time:</span> {{ $appointment->start_datetime->format('g:i A') }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Amount:</span> ₱{{ number_format($appointment->total_amount, 2) }}
                                        </div>
                                    </div>
                                    <input type="hidden" name="completed_services[]" value="{{ $appointment->id }}">
                                </div>
                                @endif
                            @endforeach
                        </div>
                        
                        @if($todaySchedule->where('status', 'completed')->count() == 0)
                        <p class="text-gray-500 text-sm italic">No completed services for today yet</p>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Additional Notes</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-600 outline-none"
                                  placeholder="Any additional notes about today's services..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Materials Used (Optional)</label>
                        <textarea name="materials_used" rows="2" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-600 outline-none"
                                  placeholder="List any materials or products used..."></textarea>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="closeServiceReportModal()" 
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-file-export mr-2"></i> Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openServiceReportModal() {
    document.getElementById('serviceReportModal').classList.remove('hidden');
}

function closeServiceReportModal() {
    document.getElementById('serviceReportModal').classList.add('hidden');
}

// Update statistics periodically
function updateStaffStatistics() {
    fetch('{{ route("staff.statistics") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('todayAppointments').textContent = data.today_appointments;
                document.getElementById('upcomingAppointments').textContent = data.upcoming_appointments;
                document.getElementById('completedThisMonth').textContent = data.completed_this_month;
                document.getElementById('monthlyCommission').textContent = '₱' + data.monthly_commission.toLocaleString('en-PH', { minimumFractionDigits: 2 });
            }
        });
}

// Update every 60 seconds
setInterval(updateStaffStatistics, 60000);
</script>
@endsection