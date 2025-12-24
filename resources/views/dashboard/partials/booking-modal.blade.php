@php
    // Fetch salon settings in the partial itself
    $salonSettings = \App\Models\SalonSetting::getSettings();
    $openingTime = $salonSettings->opening_time;
    $closingTime = $salonSettings->closing_time;
    $maxDaysAhead = $salonSettings->max_days_book_ahead;
    $slotInterval = $salonSettings->slot_interval_minutes;
@endphp

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
            <form action="{{ route('dashboard.appointments.store') }}" method="POST" id="bookingForm">
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
                        <label class="block text-gray-700 font-semibold mb-2">Staff</label>
                        <select name="staff_id" required 
                                onchange="filterServicesByStaff(this)"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="">Select Staff</option>
                            @foreach($staff ?? [] as $staffMember)
                                <option value="{{ $staffMember->id }}" 
                                        data-specialty="{{ $staffMember->specialty }}">
                                    {{ $staffMember->user->full_name }} ({{ ucfirst($staffMember->specialty) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Service</label>
                        <select name="service_id" required 
                                id="serviceSelect"
                                onchange="updateServiceDuration(this)"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="">Select Service</option>
                            @foreach($services ?? [] as $service)
                                <option value="{{ $service->id }}" 
                                        data-category="{{ $service->category->specialty }}"
                                        data-duration="{{ $service->duration_minutes }}"
                                        data-price="{{ $service->price_regular }}">
                                    {{ $service->name }} - ₱{{ number_format($service->price_regular) }} ({{ $service->duration_minutes }} mins)
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1" id="serviceFilterInfo">
                            Select a staff member first to see available services
                        </p>
                        <p class="text-sm text-gray-500 mt-1" id="durationInfo">
                            Minimum appointment duration: 30 minutes
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Date & Time</label>
                        <input 
                            type="datetime-local" 
                            name="start_datetime" 
                            required 
                            id="appointmentDateTime"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none appointment-time"
                            onchange="validateAppointmentTime(this)"
                        >
                        <p class="text-sm text-gray-500 mt-1" id="businessHoursText">
                            Business hours: {{ date('g:i A', strtotime($openingTime)) }} - {{ date('g:i A', strtotime($closingTime)) }}
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
// Filter services based on selected staff specialty
function filterServicesByStaff(staffSelect) {
    const serviceSelect = document.getElementById('serviceSelect');
    const serviceFilterInfo = document.getElementById('serviceFilterInfo');
    
    if (!staffSelect.value) {
        // Reset to all services
        Array.from(serviceSelect.options).forEach(option => {
            option.style.display = '';
        });
        serviceFilterInfo.textContent = 'All services shown';
        return;
    }
    
    const selectedStaff = staffSelect.options[staffSelect.selectedIndex];
    const staffSpecialty = selectedStaff.getAttribute('data-specialty');
    
    // Hide/show services based on category specialty
    let availableCount = 0;
    Array.from(serviceSelect.options).forEach(option => {
        if (option.value === '') {
            option.style.display = ''; // Keep the placeholder
            return;
        }
        
        const categorySpecialty = option.getAttribute('data-category');
        const matches = categorySpecialty === staffSpecialty || 
                       categorySpecialty === 'both' || 
                       staffSpecialty === 'both';
        
        if (matches) {
            option.style.display = '';
            availableCount++;
        } else {
            option.style.display = 'none';
        }
    });
    
    // Update info message
    if (availableCount > 0) {
        serviceFilterInfo.textContent = `${availableCount} ${staffSpecialty} services available`;
        serviceFilterInfo.classList.remove('text-red-600');
        serviceFilterInfo.classList.add('text-green-600');
    } else {
        serviceFilterInfo.textContent = `No ${staffSpecialty} services available for this staff`;
        serviceFilterInfo.classList.remove('text-green-600');
        serviceFilterInfo.classList.add('text-red-600');
        serviceSelect.value = '';
    }
}

// Call on page load if staff is already selected
document.addEventListener('DOMContentLoaded', function() {
    const staffSelect = document.querySelector('select[name="staff_id"]');
    if (staffSelect && staffSelect.value) {
        filterServicesByStaff(staffSelect);
    }
});

// Update duration info when service is selected
function updateServiceDuration(select) {
    const durationInfo = document.getElementById('durationInfo');
    if (!durationInfo) return;

    if (select.value) {
        const selectedOption = select.options[select.selectedIndex];
        const duration = selectedOption.getAttribute('data-duration');
        durationInfo.textContent = `Service duration: ${duration} minutes (minimum: 30 minutes)`;
        
        // Highlight if duration is less than 30 minutes
        if (parseInt(duration) < 30) {
            durationInfo.classList.remove('text-gray-500');
            durationInfo.classList.add('text-red-600', 'font-semibold');
        } else {
            durationInfo.classList.remove('text-red-600', 'font-semibold');
            durationInfo.classList.add('text-gray-500');
        }
    } else {
        durationInfo.textContent = 'Minimum appointment duration: 30 minutes';
        durationInfo.classList.remove('text-red-600', 'font-semibold');
        durationInfo.classList.add('text-gray-500');
    }
}

// Function to validate appointment time
function validateAppointmentTime(input) {
    if (!input.value) return true;
    
    const selectedDateTime = new Date(input.value);
    const selectedTime = selectedDateTime.toTimeString().split(' ')[0].substring(0, 5); // HH:mm format
    
    // Parse business hours
    const openingTime = '{{ substr($openingTime, 0, 5) }}'; // e.g., "09:00"
    const closingTime = '{{ substr($closingTime, 0, 5) }}'; // e.g., "20:00"
    
    const errorElement = document.getElementById('timeError');
    if (!errorElement) return true;
    
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
    
    // Reset duration info
    updateServiceDuration(document.querySelector('select[name="service_id"]'));

    // Get current date/time
    const now = new Date();
    const today = now.toISOString().split('T')[0];
    const currentTime = now.toTimeString().split(' ')[0].substring(0, 5); // HH:mm
    
    // Salon settings
    const openingTime = '{{ substr($openingTime, 0, 5) }}';
    const closingTime = '{{ substr($closingTime, 0, 5) }}';
    const maxDaysAhead = {{ $maxDaysAhead }};
    const slotInterval = {{ $slotInterval }};
    
    // Calculate max date
    const maxDate = new Date(now);
    maxDate.setDate(maxDate.getDate() + maxDaysAhead);
    const maxDateStr = maxDate.toISOString().split('T')[0];
    
    // Set min time
    let minTime = openingTime;
    let minDate = today;
    
    if (currentTime > openingTime && currentTime < closingTime) {
        const [currentHour, currentMinute] = currentTime.split(':').map(Number);
        const roundedMinutes = Math.ceil(currentMinute / slotInterval) * slotInterval;
        
        let roundedHour = currentHour;
        let roundedMin = roundedMinutes;
        
        if (roundedMinutes >= 60) {
            roundedHour = currentHour + 1;
            roundedMin = 0;
        }
        
        minTime = `${roundedHour.toString().padStart(2, '0')}:${roundedMin.toString().padStart(2, '0')}`;
        
        if (minTime >= closingTime) {
            const tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);
            minDate = tomorrow.toISOString().split('T')[0];
            minTime = openingTime;
        }
    }
    
    // Update the datetime input
    const dateTimeInput = document.getElementById('appointmentDateTime');
    if (dateTimeInput) {
        dateTimeInput.min = `${minDate}T${minTime}`;
        dateTimeInput.max = `${maxDateStr}T${closingTime}`;
        dateTimeInput.step = slotInterval * 60;
        dateTimeInput.value = '';
    }
    
    // Clear errors
    const errorElement = document.getElementById('timeError');
    if (errorElement) {
        errorElement.classList.add('hidden');
    }
    
    if (dateTimeInput) {
        dateTimeInput.classList.remove('border-red-500');
        dateTimeInput.classList.add('border-gray-300');
    }
}

// Form submission validation
document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            const dateTimeInput = document.getElementById('appointmentDateTime');
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

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>