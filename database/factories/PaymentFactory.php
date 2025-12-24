<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        $method = $this->faker->randomElement(['cash', 'gcash', 'bank_transfer']);
        $status = $method === 'cash' ? 'paid' : $this->faker->randomElement(['paid', 'pending', 'failed']);
        
        return [
            'appointment_id' => Appointment::factory(),
            'customer_id' => function (array $attributes) {
                return Appointment::find($attributes['appointment_id'])->customer_id;
            },
            'amount' => $this->faker->numberBetween(500, 3000),
            'method' => $method,
            'status' => $status,
            'reference_number' => $method !== 'cash' ? $this->faker->regexify('[A-Z]{4}-[0-9]{10}') : null,
            'payment_details' => $method !== 'cash' ? json_encode([
                'transaction_id' => $this->faker->uuid,
                'payment_date' => $this->faker->dateTimeThisMonth()->format('Y-m-d H:i:s'),
            ]) : null,
            'paid_at' => $status === 'paid' ? $this->faker->dateTimeThisMonth() : null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}