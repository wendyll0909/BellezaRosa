<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        User::create([
            'full_name' => 'Nina Angela Malinaw',
            'username'  => 'nina',
            'phone'     => '09171234567',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
        ]);

        // 2. Staff Users + Staff Profile
        $staffNames = ['Anna Cruz', 'Maria Santos', 'Liza Reyes'];
        $specialties = ['hair', 'nail', 'both'];
        $colors = ['#EF4444', '#3B82F6', '#10B981'];

        foreach ($staffNames as $i => $name) {
            $user = User::create([
                'full_name' => $name,
                'username'  => strtolower(str_replace(' ', '', $name)),
                'phone'     => '09' . rand(100000000, 999999999),
                'password'  => Hash::make('password'),
                'role'      => 'staff',
            ]);

            $user->staff()->create([
                'specialty'   => $specialties[$i],
                'color_code'  => $colors[$i],
            ]);
        }

        // 3. Regular Customers
        for ($i = 1; $i <= 15; $i++) {
            User::create([
                'full_name' => 'Customer ' . $i,
                'username'  => 'customer' . $i,
                'phone'     => '09' . rand(100000000, 999999999),
                'email'     => 'customer' . $i . '@example.com',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
            ]);
        }

        // 4. Run the individual seeders
        $this->call([
            ServiceCategorySeeder::class,
            CustomerSeeder::class,
            ServiceSeeder::class,
            AppointmentSeeder::class,
            AppointmentAddonSeeder::class,
        ]);
    }
}