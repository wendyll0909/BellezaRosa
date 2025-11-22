<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\AppointmentAddon;
use Illuminate\Database\Seeder;

class AppointmentAddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [];
        $appointments = Appointment::pluck('id')->toArray();

        $addonNames = [
            'Hair Treatment', 'Deep Conditioning', 'Scalp Massage', 'Hair Wash',
            'Neck Massage', 'Hand Massage', 'Hot Towel', 'Steam Treatment',
            'Extra Color', 'Toner Application', 'Olaplex Treatment', 'Keratin Shot',
            'Nail Art Design', 'French Tips', 'Nail Stamping', '3D Design',
            'Gem Application', 'Nail Jewelry', 'Callus Removal', 'Foot Scrub',
        ];

        for ($i = 1; $i <= 100; $i++) {
            $addons[] = [
                'appointment_id' => $appointments[array_rand($appointments)],
                'name' => $addonNames[array_rand($addonNames)],
                'price' => rand(5000, 50000) / 100,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        AppointmentAddon::insert($addons);
    }
}