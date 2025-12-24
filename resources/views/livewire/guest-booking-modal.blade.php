<div>
    @if($show)
        <div class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Book Your Appointment</h2>
                        <button wire:click="close" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                </div>

                <div class="p-8">
                    <form wire:submit.prevent="book">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="full_name" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" wire:model="phone" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Email (Optional)</label>
                                <input type="email" wire:model="email"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            </div>

                            <!-- Service Field (moved above Staff) -->
                            <div class="form-group">
                                <label class="block text-gray-700 font-semibold mb-2">Service *</label>
                                <select wire:model="selectedService" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                    <option value="">Select Service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">
                                            {{ $service->name }} - ₱{{ number_format($service->price_regular) }} ({{ $service->duration_minutes }} mins)
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedService') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                                <p class="text-sm text-gray-500 mt-1" id="serviceInfo">
                                    @if($selectedStaff)
                                        @php
                                            $staff = \App\Models\Staff::find($selectedStaff);
                                            $specialty = $staff ? $staff->specialty : '';
                                        @endphp
                                        Showing {{ $specialty }} services for selected staff
                                    @else
                                        Select a staff member to see available services
                                    @endif
                                </p>
                            </div>

                            <!-- Staff Field -->
                            <div class="form-group">
                                <label class="block text-gray-700 font-semibold mb-2">Staff *</label>
                                <select wire:model="selectedStaff" required
                                        wire:change="updatedSelectedStaff"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                    <option value="">Select Staff</option>
                                    @foreach($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}">
                                            {{ $staffMember->user->full_name }} ({{ ucfirst($staffMember->specialty) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedStaff') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Date *</label>
                                <input type="date" wire:model="date" wire:change="generateAvailableTimes" min="{{ now()->format('Y-m-d') }}" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Time *</label>
                                <select wire:model="time" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                        {{ empty($availableTimes) ? 'disabled' : '' }}>
                                    <option value="">Select Time</option>
                                    @if(!empty($availableTimes))
                                        @foreach($availableTimes as $slot)
                                            <option value="{{ $slot }}">{{ \Carbon\Carbon::createFromFormat('H:i', $slot)->format('g:i A') }}</option>
                                        @endforeach
                                    @else
                                        <option value="">No available times - select date and staff first</option>
                                    @endif
                                </select>
                                @error('time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                
                                @if($selectedStaff && $date && empty($availableTimes))
                                    <p class="text-orange-500 text-sm mt-2">
                                        No available time slots for selected date and staff. Please try another date or staff member.
                                    </p>
                                @endif
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-gray-700 font-semibold mb-2">Notes (Optional)</label>
                                <textarea wire:model="notes" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-4">
                            <button type="button" wire:click="close"
                                    class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-10 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition"
                                    {{ empty($availableTimes) ? 'disabled' : '' }}>
                                <i class="fas fa-calendar-check mr-2"></i> Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif
</div>