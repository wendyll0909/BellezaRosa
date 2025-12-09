<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Get all appointments that should have payments (completed appointments)
        $appointments = Appointment::where('status', 'completed')
            ->with('customer')
            ->get();

        $payments = [];
        
        foreach ($appointments as $appointment) {
            $method = $this->getRandomPaymentMethod();
            $status = $this->getPaymentStatus($method);
            
            $paymentDetails = null;
            $referenceNumber = null;
            
            // Generate reference number for non-cash payments
            if ($method !== 'cash') {
                $referenceNumber = $this->generateReferenceNumber($method);
                $paymentDetails = $this->generatePaymentDetails($method);
            }
            
            // Determine paid_at timestamp based on appointment date and status
            $paidAt = null;
            if ($status === 'paid') {
                $paidAt = Carbon::parse($appointment->start_datetime)
                    ->addHours(rand(0, 2))
                    ->addMinutes(rand(0, 59));
            }
            
            $payments[] = [
                'appointment_id' => $appointment->id,
                'customer_id' => $appointment->customer_id,
                'amount' => $appointment->total_amount,
                'method' => $method,
                'status' => $status,
                'reference_number' => $referenceNumber,
                'payment_details' => $paymentDetails ? json_encode($paymentDetails) : null,
                'paid_at' => $paidAt,
                'notes' => $this->getPaymentNotes($status, $method),
                'created_at' => $appointment->created_at,
                'updated_at' => now(),
            ];
            
            // Update appointment payment method to match
            $appointment->update(['payment_method' => $method]);
        }
        
        // Insert all payments
        Payment::insert($payments);
        
        $this->command->info('PaymentSeeder: Created ' . count($payments) . ' payments for completed appointments.');
        
        // Also create some pending payments for other appointment statuses
        $this->createPendingPayments();
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
            return rand(1, 100) <= 90 ? 'paid' : 'pending'; // 90% paid, 10% pending
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
        
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(Str::random(8));
    }
    
    private function generatePaymentDetails(string $method): array
    {
        $firstNames = ['Juan', 'Maria', 'Pedro', 'Ana', 'Luis', 'Carmen', 'Jose', 'Elena', 'Miguel', 'Rosa'];
        $lastNames = ['Dela Cruz', 'Santos', 'Reyes', 'Garcia', 'Gonzales', 'Ramos', 'Torres', 'Fernandez', 'Rivera', 'Mendoza'];
        
        return match($method) {
            'gcash' => [
                'mobile_number' => '09' . rand(100000000, 999999999),
                'transaction_id' => 'TX' . rand(1000000000, 9999999999),
                'payment_date' => now()->subDays(rand(1, 30))->format('Y-m-d H:i:s'),
            ],
            'bank_transfer' => [
                'bank_name' => ['BPI', 'BDO', 'Metrobank', 'Security Bank'][array_rand([0, 1, 2, 3])],
                'account_number' => rand(1000000000, 9999999999),
                'account_name' => $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)],
                'transfer_date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
            ],
            default => []
        };
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
    
    private function createPendingPayments(): void
    {
        // Create some pending payments for confirmed appointments
        $confirmedAppointments = Appointment::where('status', 'confirmed')
            ->whereDoesntHave('payment')
            ->limit(rand(5, 10))
            ->with('customer')
            ->get();
        
        $pendingPayments = [];
        
        foreach ($confirmedAppointments as $appointment) {
            $method = $this->getRandomPaymentMethod();
            
            $pendingPayments[] = [
                'appointment_id' => $appointment->id,
                'customer_id' => $appointment->customer_id,
                'amount' => $appointment->total_amount,
                'method' => $method,
                'status' => 'pending',
                'reference_number' => $method !== 'cash' ? $this->generateReferenceNumber($method) : null,
                'payment_details' => null,
                'paid_at' => null,
                'notes' => 'Payment pending - awaiting customer payment',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($pendingPayments)) {
            Payment::insert($pendingPayments);
            $this->command->info('PaymentSeeder: Created ' . count($pendingPayments) . ' pending payments.');
        }
    }
}