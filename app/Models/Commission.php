<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'staff_id',
        'appointment_id',
        'service_amount',
        'commission_rate',
        'amount',
        'status',
        'payment_date',
        'notes'
    ];

    protected $casts = [
        'service_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'payment_date' => 'date'
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Helper methods
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function markAsPaid($paymentDate = null)
    {
        $this->update([
            'status' => 'paid',
            'payment_date' => $paymentDate ?? now()
        ]);
    }

    // Calculate commission amount
    public static function calculateCommission($serviceAmount, $commissionRate)
    {
        return ($serviceAmount * $commissionRate) / 100;
    }
}