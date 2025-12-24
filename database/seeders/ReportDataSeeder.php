<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\InventoryItem;
use App\Models\InventoryUpdate;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Service;
use Carbon\Carbon;

class ReportDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing data
        $customers = Customer::all()->pluck('id')->toArray();
        $staff = Staff::all()->pluck('id')->toArray();
        $services = Service::all()->pluck('id')->toArray();
        
        if (empty($customers) || empty($staff) || empty($services)) {
            $this->command->info('ReportDataSeeder: Insufficient data. Run other seeders first.');
            return;
        }

        // Create sample appointments for the past 30 days
        $appointments = [];
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // Create 3-8 appointments per day
            $appointmentCount = rand(3, 8);
            for ($j = 0; $j < $appointmentCount; $j++) {
                $service = Service::find($services[array_rand($services)]);
                $startTime = $date->copy()->setTime(rand(9, 17), rand(0, 11) * 5);
                $duration = $service->duration_minutes ?? 60;
                $endTime = $startTime->copy()->addMinutes($duration);
                
                $status = $this->getWeightedStatus();
                $isCompleted = $status === 'completed';
                
                $appointments[] = [
                    'customer_id' => $customers[array_rand($customers)],
                    'staff_id' => $staff[array_rand($staff)],
                    'service_id' => $service->id,
                    'start_datetime' => $startTime,
                    'end_datetime' => $endTime,
                    'status' => $status,
                    'payment_method' => $this->getRandomPaymentMethod(),
                    'total_amount' => $service->price_regular ?? rand(500, 3000),
                    'notes' => rand(1, 5) === 1 ? 'Special request from customer' : null,
                    'is_walk_in' => rand(1, 5) === 1,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }

        // Insert appointments
        Appointment::insert($appointments);
        $this->command->info('ReportDataSeeder: Created ' . count($appointments) . ' appointments.');

        // Create payments for completed appointments
        $completedAppointments = Appointment::where('status', 'completed')->get();
        $payments = [];
        
        foreach ($completedAppointments as $appointment) {
            $method = $this->getRandomPaymentMethod();
            $status = $this->getPaymentStatus($method);
            
            $payments[] = [
                'appointment_id' => $appointment->id,
                'customer_id' => $appointment->customer_id,
                'amount' => $appointment->total_amount,
                'method' => $method,
                'status' => $status,
                'reference_number' => $method !== 'cash' ? $this->generateReferenceNumber($method) : null,
                'payment_details' => $method !== 'cash' ? json_encode([
                    'transaction_id' => 'TX' . rand(1000000000, 9999999999),
                    'payment_date' => $appointment->start_datetime->format('Y-m-d H:i:s'),
                ]) : null,
                'paid_at' => $status === 'paid' ? $appointment->start_datetime->copy()->addMinutes(rand(30, 120)) : null,
                'notes' => $this->getPaymentNotes($status, $method),
                'created_at' => $appointment->created_at,
                'updated_at' => now(),
            ];
        }
        
        Payment::insert($payments);
        $this->command->info('ReportDataSeeder: Created ' . count($payments) . ' payments.');

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
                    'updated_by' => 1, // Admin user ID
                    'update_date' => $date,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
            
            // Update final stock
            $item->current_stock = $currentStock;
            $item->save();
        }
        
        $this->command->info('ReportDataSeeder: Created inventory updates.');
    }
    
    private function getWeightedStatus(): string
    {
        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $weights = [10, 15, 60, 10, 5]; // Weighted probabilities
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
    
    private function getPaymentStatus(string $method): string
    {
        // Cash payments are usually paid immediately
        if ($method === 'cash') {
            return rand(1, 100) <= 90 ? 'paid' : 'pending';
        }
        
        // For digital payments
        $random = rand(1, 100);
        if ($random <= 80) { // 80% paid
            return 'paid';
        } elseif ($random <= 95) { // 15% pending
            return 'pending';
        } else { // 5% failed
            return 'failed';
        }
    }
    
    private function generateReferenceNumber(string $method): string
    {
        $prefix = match($method) {
            'gcash' => 'GCASH',
            'bank_transfer' => 'BANK',
            default => 'REF'
        };
        
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(8));
    }
    
    private function getPaymentNotes(string $status, string $method): ?string
    {
        if ($status === 'failed') {
            return match($method) {
                'gcash' => 'Transaction declined by GCash system',
                'bank_transfer' => 'Bank transfer not received',
                'cash' => 'Customer did not have exact amount',
                default => 'Payment failed'
            };
        }
        
        if ($status === 'paid' && $method !== 'cash') {
            return match($method) {
                'gcash' => 'Verified via GCash app',
                'bank_transfer' => 'Confirmed via bank statement',
                default => null
            };
        }
        
        return null;
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