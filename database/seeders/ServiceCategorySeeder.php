<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Hair Services',
                'description' => 'All hair-related services',
                'specialty' => 'hair',
                'is_active' => true
            ],
            [
                'name' => 'Nail Services',
                'description' => 'All nail-related services',
                'specialty' => 'nail',
                'is_active' => true
            ],
            [
                'name' => 'Full Service',
                'description' => 'Combined hair and nail services',
                'specialty' => 'both',
                'is_active' => true
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }
    }
}