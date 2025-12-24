<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = [];
        $customers = Customer::pluck('id')->toArray();
        $staff = Staff::pluck('id')->toArray();
        $services = Service::all(); // Get services to access duration

        $statuses = ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
        $paymentMethods = ['cash', 'gcash', 'bank_transfer', 'online', 'unpaid'];

        for ($i = 1; $i <= 100; $i++) {
            // Generate dates within reasonable range
            $daysAgo = rand(0, 60); // Appointments from today to 60 days ago
            $daysFuture = rand(0, 30); // Some future appointments
            
            $isFuture = rand(0, 1); // 50% chance of future appointment
            $baseDate = $isFuture ? 
                Carbon::now()->addDays($daysFuture) : 
                Carbon::now()->subDays($daysAgo);
            
            // Random time between 9 AM and 7 PM
            $hour = rand(9, 18); // 9 AM to 6 PM
            $minute = [0, 15, 30, 45][array_rand([0, 15, 30, 45])];
            
            $startDateTime = $baseDate->copy()->setTime($hour, $minute);
            
            // Get random service
            $service = $services->random();
            $duration = $service->duration_minutes;
            $endDateTime = $startDateTime->copy()->addMinutes($duration);
            
            // Determine status based on date
            $status = $this->determineStatus($startDateTime, $isFuture);
            
            // Only completed appointments should have non-unpaid payment methods
            $paymentMethod = $status === 'completed' ? 
                $paymentMethods[array_rand(['cash', 'gcash', 'bank_transfer', 'online'])] : 
                'unpaid';
            
            $appointments[] = [
                'customer_id' => $customers[array_rand($customers)],
                'staff_id' => $staff[array_rand($staff)],
                'service_id' => $service->id,
                'start_datetime' => $startDateTime,
                'end_datetime' => $endDateTime,
                'status' => $status,
                'payment_method' => $paymentMethod,
                'total_amount' => $service->price_regular ?? rand(10000, 500000) / 100,
                'notes' => rand(1, 3) === 1 ? 'Special request from customer' : null,
                'is_walk_in' => rand(1, 5) === 1,
                'cancellation_reason' => $status === 'cancelled' ? $this->getCancellationReason() : null,
                'created_at' => $startDateTime->copy()->subDays(rand(1, 7)),
                'updated_at' => now(),
            ];
        }

        Appointment::insert($appointments);
    }
    
    private function determineStatus(Carbon $date, bool $isFuture): string
    {
        if ($isFuture) {
            // Future appointments can be scheduled or confirmed
            return rand(1, 100) <= 70 ? 'scheduled' : 'confirmed';
        }
        
        // Past appointments
        $rand = rand(1, 100);
        if ($rand <= 60) { // 60% completed
            return 'completed';
        } elseif ($rand <= 80) { // 20% cancelled
            return 'cancelled';
        } elseif ($rand <= 90) { // 10% no-show
            return 'no_show';
        } else { // 10% in_progress (for appointments happening right now)
            return 'in_progress';
        }
    }
    
    private function getCancellationReason(): string
    {
        $reasons = [
            'Customer rescheduled',
            'Staff unavailable',
            'Emergency situation',
            'Customer not feeling well',
            'Weather conditions',
            'Double booked by mistake',
            'Customer changed mind',
            'Salon emergency',
        ];
        
        return $reasons[array_rand($reasons)];
    }
}