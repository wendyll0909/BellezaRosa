<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [];
        $genders = ['male', 'female', 'other'];
        $maleFirstNames = ['John', 'Michael', 'David', 'James', 'Robert', 'William', 'Richard', 'Joseph', 'Thomas', 'Charles'];
        $femaleFirstNames = ['Mary', 'Jennifer', 'Linda', 'Patricia', 'Elizabeth', 'Susan', 'Jessica', 'Sarah', 'Karen', 'Nancy'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];

        for ($i = 1; $i <= 100; $i++) {
            $gender = $genders[array_rand($genders)];
            $firstName = $gender === 'male' ? $maleFirstNames[array_rand($maleFirstNames)] : $femaleFirstNames[array_rand($femaleFirstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            
            $customers[] = [
                'user_id' => null,
                'full_name' => "{$firstName} {$lastName}",
                'phone' => '09' . rand(100000000, 999999999),
                'email' => Str::lower($firstName) . '.' . Str::lower($lastName) . $i . '@example.com',
                'gender' => $gender,
                'birth_date' => date('Y-m-d', rand(strtotime('-60 years'), strtotime('-18 years'))),
                'notes' => rand(1, 3) === 1 ? 'Regular customer' : null,
                'total_visits' => rand(0, 100),
                'total_spent' => rand(0, 50000) / 100,
                'last_visit' => rand(1, 3) === 1 ? null : date('Y-m-d H:i:s', rand(strtotime('-1 year'), time())),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Customer::insert($customers);
    }
}