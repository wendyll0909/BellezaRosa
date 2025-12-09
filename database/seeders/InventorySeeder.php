<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $inventoryItems = [];
        
        // Salon Inventory Items - Realistic for Belleza Rosa Salon
        $salonItems = [
            // Hair Care Category
            [
                'name' => 'Professional Shampoo',
                'category' => 'hair_care',
                'current_stock' => rand(15, 50),
                'minimum_stock' => 10,
                'unit' => 'bottles',
                'cost' => 350.00,
                'supplier' => 'L\'Oreal Professional'
            ],
            [
                'name' => 'Color Developer',
                'category' => 'hair_care',
                'current_stock' => rand(20, 60),
                'minimum_stock' => 15,
                'unit' => 'bottles',
                'cost' => 280.00,
                'supplier' => 'Wella Professionals'
            ],
            [
                'name' => 'Hair Color Tubes',
                'category' => 'hair_care',
                'current_stock' => rand(30, 80),
                'minimum_stock' => 20,
                'unit' => 'tubes',
                'cost' => 150.00,
                'supplier' => 'Schwarzkopf'
            ],
            [
                'name' => 'Conditioner',
                'category' => 'hair_care',
                'current_stock' => rand(10, 40),
                'minimum_stock' => 8,
                'unit' => 'bottles',
                'cost' => 320.00,
                'supplier' => 'Kerastase'
            ],
            [
                'name' => 'Hair Treatment Masks',
                'category' => 'hair_care',
                'current_stock' => rand(25, 50),
                'minimum_stock' => 12,
                'unit' => 'sachets',
                'cost' => 45.00,
                'supplier' => 'Moroccanoil'
            ],
            
            // Nail Care Category
            [
                'name' => 'Nail Polish - Red',
                'category' => 'nail_care',
                'current_stock' => rand(8, 25),
                'minimum_stock' => 5,
                'unit' => 'bottles',
                'cost' => 120.00,
                'supplier' => 'OPI'
            ],
            [
                'name' => 'Gel Polish',
                'category' => 'nail_care',
                'current_stock' => rand(15, 40),
                'minimum_stock' => 10,
                'unit' => 'bottles',
                'cost' => 180.00,
                'supplier' => 'CND Shellac'
            ],
            [
                'name' => 'Nail Tips',
                'category' => 'nail_care',
                'current_stock' => rand(50, 150),
                'minimum_stock' => 30,
                'unit' => 'pcs',
                'cost' => 5.00,
                'supplier' => 'Kupa'
            ],
            [
                'name' => 'Nail Glue',
                'category' => 'nail_care',
                'current_stock' => rand(10, 30),
                'minimum_stock' => 6,
                'unit' => 'tubes',
                'cost' => 85.00,
                'supplier' => 'Nailene'
            ],
            [
                'name' => 'Cuticle Oil',
                'category' => 'nail_care',
                'current_stock' => rand(8, 20),
                'minimum_stock' => 4,
                'unit' => 'bottles',
                'cost' => 95.00,
                'supplier' => 'CND'
            ],
            
            // Skin Care Category
            [
                'name' => 'Facial Cleanser',
                'category' => 'skin_care',
                'current_stock' => rand(10, 25),
                'minimum_stock' => 8,
                'unit' => 'bottles',
                'cost' => 280.00,
                'supplier' => 'Dermalogica'
            ],
            [
                'name' => 'Facial Masks',
                'category' => 'skin_care',
                'current_stock' => rand(20, 50),
                'minimum_stock' => 15,
                'unit' => 'sachets',
                'cost' => 60.00,
                'supplier' => 'The Face Shop'
            ],
            [
                'name' => 'Moisturizer',
                'category' => 'skin_care',
                'current_stock' => rand(8, 18),
                'minimum_stock' => 5,
                'unit' => 'tubes',
                'cost' => 350.00,
                'supplier' => 'Cetaphil'
            ],
            [
                'name' => 'Sunscreen',
                'category' => 'skin_care',
                'current_stock' => rand(12, 30),
                'minimum_stock' => 10,
                'unit' => 'bottles',
                'cost' => 420.00,
                'supplier' => 'La Roche-Posay'
            ],
            
            // Tools Category
            [
                'name' => 'Hair Scissors',
                'category' => 'tools',
                'current_stock' => rand(3, 8),
                'minimum_stock' => 2,
                'unit' => 'pcs',
                'cost' => 850.00,
                'supplier' => 'Hikari'
            ],
            [
                'name' => 'Hair Clippers',
                'category' => 'tools',
                'current_stock' => rand(2, 5),
                'minimum_stock' => 1,
                'unit' => 'pcs',
                'cost' => 1200.00,
                'supplier' => 'Wahl'
            ],
            [
                'name' => 'Nail Files',
                'category' => 'tools',
                'current_stock' => rand(30, 100),
                'minimum_stock' => 20,
                'unit' => 'pcs',
                'cost' => 8.00,
                'supplier' => 'Mont Bleu'
            ],
            [
                'name' => 'Nail Buffers',
                'category' => 'tools',
                'current_stock' => rand(20, 50),
                'minimum_stock' => 15,
                'unit' => 'pcs',
                'cost' => 12.00,
                'supplier' => 'Beetles'
            ],
            [
                'name' => 'Makeup Brushes',
                'category' => 'tools',
                'current_stock' => rand(5, 15),
                'minimum_stock' => 3,
                'unit' => 'sets',
                'cost' => 450.00,
                'supplier' => 'Morphe'
            ],
            
            // Other Category
            [
                'name' => 'Disposable Capes',
                'category' => 'other',
                'current_stock' => rand(100, 300),
                'minimum_stock' => 50,
                'unit' => 'pcs',
                'cost' => 3.50,
                'supplier' => 'Sally Beauty'
            ],
            [
                'name' => 'Towels',
                'category' => 'other',
                'current_stock' => rand(20, 50),
                'minimum_stock' => 15,
                'unit' => 'pcs',
                'cost' => 65.00,
                'supplier' => 'Costco'
            ],
            [
                'name' => 'Disposable Gloves',
                'category' => 'other',
                'current_stock' => rand(200, 500),
                'minimum_stock' => 100,
                'unit' => 'pcs',
                'cost' => 0.50,
                'supplier' => 'Medline'
            ],
            [
                'name' => 'Sanitizing Spray',
                'category' => 'other',
                'current_stock' => rand(10, 25),
                'minimum_stock' => 8,
                'unit' => 'bottles',
                'cost' => 180.00,
                'supplier' => 'Clorox'
            ],
        ];
        
        foreach ($salonItems as $itemData) {
            $inventoryItems[] = array_merge($itemData, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Insert all items
        InventoryItem::insert($inventoryItems);
        
        $this->command->info('InventorySeeder: Created ' . count($inventoryItems) . ' inventory items.');
    }
}