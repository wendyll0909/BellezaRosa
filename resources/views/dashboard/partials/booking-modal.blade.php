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