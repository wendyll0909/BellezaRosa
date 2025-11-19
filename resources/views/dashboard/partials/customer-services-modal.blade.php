<!-- Customer Services Modal -->
<div id="customerServicesModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-screen overflow-y-auto">
        <div class="bg-gradient-to-r from-green-900 to-green-700 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">Customer Services Report</h2>
                <button onclick="closeModal('customerServicesModal')" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <!-- Modal content remains the same as before -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Services Availed by Customers</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $totalCustomers ?? 0 }}</div>
                        <div class="text-sm text-blue-800">Total Customers</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $totalServices ?? 0 }}</div>
                        <div class="text-sm text-green-800">Services Booked</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ $popularService ?? 'N/A' }}</div>
                        <div class="text-sm text-purple-800">Most Popular Service</div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Customer</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Services Availed</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Total Visits</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Total Spent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($customersWithServices as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $customer->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $customer->phone }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($customer->appointments->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($customer->appointments->take(3) as $appointment)
                                            <div class="text-sm text-gray-700">
                                                • {{ $appointment->service->name }}
                                                <span class="text-xs text-gray-500">({{ $appointment->start_datetime->format('M j') }})</span>
                                            </div>
                                        @endforeach
                                        @if($customer->appointments->count() > 3)
                                            <div class="text-xs text-blue-600">
                                                +{{ $customer->appointments->count() - 3 }} more services
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No services availed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $customer->total_visits }}
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-green-600">
                                ₱{{ number_format($customer->total_spent, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>