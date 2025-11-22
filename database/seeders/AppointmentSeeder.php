<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Service;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = [];
        $customers = Customer::pluck('id')->toArray();
        $staff = Staff::pluck('id')->toArray();
        $services = Service::pluck('id')->toArray();

        $statuses = ['scheduled', 'confirmed', 'in_progress', 'completed', 'no_show', 'cancelled'];
        $paymentMethods = ['cash', 'gcash', 'bank_transfer', 'online', 'unpaid'];

        for ($i = 1; $i <= 100; $i++) {
            $startDateTime = date('Y-m-d H:i:s', rand(strtotime('today'), strtotime('+1 months')));
            $duration = [30, 45, 60, 75, 90, 120][array_rand([30, 45, 60, 75, 90, 120])];
            $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime) + ($duration * 60));
            
            $appointments[] = [
                'customer_id' => $customers[array_rand($customers)],
                'staff_id' => $staff[array_rand($staff)],
                'service_id' => $services[array_rand($services)],
                'start_datetime' => $startDateTime,
                'end_datetime' => $endDateTime,
                'status' => $statuses[array_rand($statuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'total_amount' => rand(10000, 500000) / 100,
                'notes' => rand(1, 3) === 1 ? 'Special request from customer' : null,
                'is_walk_in' => rand(1, 5) === 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Appointment::insert($appointments);
    }
}