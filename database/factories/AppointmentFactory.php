<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'staff_id' => Staff::factory(),
            'service_id' => Service::factory(),
            'start_datetime' => $this->faker->dateTimeBetween('-30 days', '+30 days'),
            'end_datetime' => function (array $attributes) {
                $service = Service::find($attributes['service_id']);
                $duration = $service ? $service->duration_minutes : 60;
                return (clone $attributes['start_datetime'])->modify("+{$duration} minutes");
            },
            'status' => $this->faker->randomElement(['scheduled', 'confirmed', 'completed', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['cash', 'gcash', 'bank_transfer', 'unpaid']),
            'total_amount' => $this->faker->numberBetween(500, 3000),
            'notes' => $this->faker->optional()->sentence(),
            'is_walk_in' => $this->faker->boolean(20),
        ];
    }
}