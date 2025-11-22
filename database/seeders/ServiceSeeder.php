<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [];
        $categories = ServiceCategory::pluck('id')->toArray();

        $serviceNames = [
            // Hair Services
            'Regular Haircut', 'Stylist Haircut', 'Kids Haircut', 'Senior Haircut',
            'Blow Dry', 'Hair Wash & Blow Dry', 'Iron Styling', 'Updo Hairstyle',
            'Bridal Hairstyle', 'Formal Hairstyle', 'Single Process Color', 'Full Highlight',
            'Partial Highlight', 'Balayage', 'Ombre', 'Color Correction', 'Root Touch Up',
            'Toner', 'Deep Conditioning', 'Protein Treatment', 'Scalp Treatment',
            'Hair Spa', 'Anti-Dandruff Treatment', 'Hair Fall Treatment', 'Keratin Smoothing',
            
            // Nail Services
            'Regular Manicure', 'Spa Manicure', 'Gel Manicure', 'French Manicure',
            'Acrylic Full Set', 'Acrylic Fill', 'Dip Powder Manicure', 'Nail Art Design',
            'Regular Pedicure', 'Spa Pedicure', 'Gel Pedicure', 'French Pedicure',
            'Medical Pedicure', 'Callus Treatment', 'Paraffin Pedicure', 'Nail Repair',
            
            // Other Services
            'Full Body Wax', 'Brazilian Wax', 'Bikini Wax', 'Underarm Wax',
            'Basic Facial', 'Acne Treatment Facial', 'Anti-Aging Facial', 'Hydrating Facial',
            'Swedish Massage', 'Deep Tissue Massage', 'Aromatherapy Massage', 'Hot Stone Massage',
            'Full Makeup', 'Bridal Makeup', 'Evening Makeup', 'Airbrush Makeup',
        ];

        for ($i = 0; $i < 50; $i++) {
            $isPremium = rand(1, 100) <= 30;
            $priceRegular = rand(10000, 200000) / 100;
            $pricePremium = $isPremium ? $priceRegular * 1.5 : null;
            
            $services[] = [
                'category_id' => $categories[array_rand($categories)],
                'name' => $serviceNames[$i] ?? "Service " . ($i + 1),
                'duration_minutes' => [30, 45, 60, 75, 90, 120, 150, 180][array_rand([30, 45, 60, 75, 90, 120, 150, 180])],
                'price_regular' => $priceRegular,
                'price_premium' => $pricePremium,
                'is_premium' => $isPremium,
                'description' => rand(1, 5) !== 1 ? 'Professional ' . ($serviceNames[$i] ?? "Service " . ($i + 1)) . ' service' : null,
                'consumables' => rand(1, 2) === 1 ? json_encode([
                    'shampoo' => rand(1, 5),
                    'conditioner' => rand(1, 3)
                ]) : null,
                'is_active' => rand(1, 10) !== 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Service::insert($services);
    }
}