<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\InventoryItem;
use App\Models\InventoryUpdate;
use Carbon\Carbon;

class ReportDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample appointments for the past 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // Create 3-8 appointments per day
            $appointmentCount = rand(3, 8);
            for ($j = 0; $j < $appointmentCount; $j++) {
                $appointment = Appointment::factory()->create([
                    'start_datetime' => $date->copy()->setTime(rand(9, 17), rand(0, 11) * 5),
                    'status' => $this->getRandomStatus(),
                    'total_amount' => rand(500, 3000),
                ]);
                
                // Create payment for some appointments
                if (rand(0, 1)) {
                    Payment::factory()->create([
                        'appointment_id' => $appointment->id,
                        'customer_id' => $appointment->customer_id,
                        'amount' => $appointment->total_amount,
                        'paid_at' => $appointment->start_datetime->addMinutes(rand(30, 120)),
                        'status' => 'paid',
                        'method' => $this->getRandomPaymentMethod(),
                    ]);
                }
            }
        }
        
        // Create inventory updates
        $items = InventoryItem::all();
        foreach ($items as $item) {
            $currentStock = $item->current_stock;
            
            // Create 5-10 random updates for each item
            $updateCount = rand(5, 10);
            for ($i = 0; $i < $updateCount; $i++) {
                $date = Carbon::now()->subDays(rand(0, 30));
                $type = rand(0, 1) ? 'add' : 'subtract';
                $quantity = rand(1, 20);
                
                $previousStock = $currentStock;
                
                if ($type === 'add') {
                    $currentStock += $quantity;
                } else {
                    $currentStock = max(0, $currentStock - $quantity);
                }
                
                InventoryUpdate::create([
                    'item_id' => $item->id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'previous_stock' => $previousStock,
                    'new_stock' => $currentStock,
                    'remark' => $this->getRandomRemark($type),
                    'updated_by' => 1, // Assuming admin user ID is 1
                    'update_date' => $date,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
            
            // Update final stock
            $item->current_stock = $currentStock;
            $item->save();
        }
    }
    
    private function getRandomStatus(): string
    {
        $statuses = ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
        $weights = [10, 15, 10, 50, 10, 5]; // Weighted probabilities
        $rand = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'completed';
    }
    
    private function getRandomPaymentMethod(): string
    {
        $methods = ['cash', 'gcash', 'bank_transfer'];
        return $methods[array_rand($methods)];
    }
    
    private function getRandomRemark(string $type): string
    {
        $addRemarks = [
            'New shipment arrived',
            'Restocked from supplier',
            'Purchase order received',
            'Manual adjustment - addition',
            'Return from customer',
            'Found additional stock',
        ];
        
        $subtractRemarks = [
            'Used for customer service',
            'Damaged item removed',
            'Expired product discarded',
            'Sold to customer',
            'Internal use',
            'Theft/loss adjustment',
            'Quality control removal',
        ];
        
        if ($type === 'add') {
            return $addRemarks[array_rand($addRemarks)];
        }
        
        return $subtractRemarks[array_rand($subtractRemarks)];
    }
}