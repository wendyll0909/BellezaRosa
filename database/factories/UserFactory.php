<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'phone'     => fake()->unique()->phoneNumber(),
            'password'  => Hash::make('password'), // default password
            'role'      => 'customer',
            'remember_token' => Str::random(10),
        ];
    }

    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'full_name' => 'Nina Angela Malinaw',
            'username'  => 'nina',
            'phone'     => '09171234567',
            'role'      => 'admin',
        ]);
    }
}