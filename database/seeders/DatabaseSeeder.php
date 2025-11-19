<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        User::create([
            'full_name' => 'Nina Angela Malinaw',
            'username'  => 'nina',
            'phone'     => '09171234567',
            'password'  => bcrypt('password'),
            'role'      => 'admin',
        ]);

        // 2. Staff Users + Staff Profile
        $staffNames = ['Anna Cruz', 'Maria Santos', 'Liza Reyes'];
        $specialties = ['hair', 'nail', 'both']; // Changed 'nails' to 'nail'
        $colors = ['#EF4444', '#3B82F6', '#10B981'];

        foreach ($staffNames as $i => $name) {
            $user = User::create([
                'full_name' => $name,
                'username'  => strtolower(str_replace(' ', '', $name)),
                'phone'     => '09' . fake()->unique()->randomNumber(9, true),
                'password'  => bcrypt('password'),
                'role'      => 'staff',
            ]);

            $user->staff()->create([
                'specialty'   => $specialties[$i],
                'color_code'  => $colors[$i],
            ]);
        }

        // 3. Regular Customers
        User::factory(15)->create([
            'role' => 'customer',
        ]);

        
    }
}