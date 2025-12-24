<?php

namespace Database\Seeders;

use App\Models\Commission;
use App\Models\Appointment;
use App\Models\Staff;
use App\Models\SalonSetting;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CommissionSeeder extends Seeder
{
    public function run(): void
    {
        // Get salon settings for default commission rate
        $settings = SalonSetting::first();
        $defaultRate = $settings ? $settings->default_commission_rate : 30.00;
        
        // Get completed appointments
        $completedAppointments = Appointment::where('status', 'completed')
            ->with('staff')
            ->get();
        
        $commissions = [];
        
        foreach ($completedAppointments as $appointment) {
            // Skip if commission already exists
            if (Commission::where('appointment_id', $appointment->id)->exists()) {
                continue;
            }
            
            // Determine commission rate (could vary per staff)
            $commissionRate = $defaultRate;
            $commissionAmount = ($appointment->total_amount * $commissionRate) / 100;
            
            // Determine if commission is paid (older appointments more likely)
            $daysSince = Carbon::parse($appointment->start_datetime)->diffInDays(now());
            $status = $daysSince > 15 ? 'paid' : (rand(1, 100) <= 30 ? 'paid' : 'pending');
            
            $commissions[] = [
                'staff_id' => $appointment->staff_id,
                'appointment_id' => $appointment->id,
                'service_amount' => $appointment->total_amount,
                'commission_rate' => $commissionRate,
                'amount' => $commissionAmount,
                'status' => $status,
                'payment_date' => $status === 'paid' ? 
                    Carbon::parse($appointment->start_datetime)->addDays(rand(1, 30)) : 
                    null,
                'notes' => 'Commission for completed appointment',
                'created_at' => $appointment->start_datetime,
                'updated_at' => now(),
            ];
        }
        
        if (!empty($commissions)) {
            Commission::insert($commissions);
            $this->command->info('CommissionSeeder: Created ' . count($commissions) . ' commission records.');
        } else {
            $this->command->info('CommissionSeeder: No completed appointments found for commissions.');
        }
        
        // Also create some commission payments for staff
        $this->createStaffCommissions();
    }
    
    private function createStaffCommissions(): void
    {
        $staff = Staff::all();
        
        foreach ($staff as $staffMember) {
            // Get staff's pending commissions
            $pendingCommissions = Commission::where('staff_id', $staffMember->id)
                ->pending()
                ->get();
            
            // Pay some of them
            foreach ($pendingCommissions as $commission) {
                if (rand(1, 100) <= 40) { // 40% chance to pay each pending commission
                    $commission->update([
                        'status' => 'paid',
                        'payment_date' => Carbon::parse($commission->created_at)->addDays(rand(1, 30)),
                        'notes' => 'Commission paid via ' . ['cash', 'bank_transfer', 'gcash'][array_rand([0, 1, 2])]
                    ]);
                }
            }
        }
        
        $this->command->info('CommissionSeeder: Updated commission payment statuses.');
    }
}