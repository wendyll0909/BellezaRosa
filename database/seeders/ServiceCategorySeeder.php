<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hair Cutting', 'display_order' => 1],
            ['name' => 'Hair Coloring', 'display_order' => 2],
            ['name' => 'Hair Treatment', 'display_order' => 3],
            ['name' => 'Hair Styling', 'display_order' => 4],
            ['name' => 'Manicure', 'display_order' => 5],
            ['name' => 'Pedicure', 'display_order' => 6],
            ['name' => 'Nail Art', 'display_order' => 7],
            ['name' => 'Nail Extension', 'display_order' => 8],
            ['name' => 'Waxing', 'display_order' => 9],
            ['name' => 'Facial', 'display_order' => 10],
            ['name' => 'Massage', 'display_order' => 11],
            ['name' => 'Makeup', 'display_order' => 12],
            ['name' => 'Eyelash Extension', 'display_order' => 13],
            ['name' => 'Eyebrow Shaping', 'display_order' => 14],
        ];

        ServiceCategory::insert($categories);
    }
}