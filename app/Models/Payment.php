<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'appointment_id',
        'customer_id',
        'amount',
        'method',
        'status',
        'reference_number',
        'payment_details',
        'paid_at',
        'notes'
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'payment_details' => 'array',
        'paid_at'         => 'datetime'
    ];

    protected $appends = ['is_paid', 'is_failed', 'is_cancelled'];

    // Accessors
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }
    
    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }
    
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    // Relationships
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Helper methods
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
    
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    // Action methods
    public function markAsPaid(): void
    {
        $this->update([
            'status'  => 'paid',
            'paid_at' => now()
        ]);
    }
    
    public function markAsFailed(string $reason = null): void
    {
        $notes = $reason 
            ? ($this->notes ? $this->notes . "\n" . $reason : $reason)
            : $this->notes;

        $this->update([
            'status' => 'failed',
            'notes'  => $notes
        ]);
    }
    
    public function markAsCancelled(string $reason = null): void
    {
        $notes = $reason 
            ? ($this->notes ? $this->notes . "\n" . $reason : $reason)
            : $this->notes;

        $this->update([
            'status' => 'cancelled',
            'notes'  => $notes
        ]);
    }

    // Scopes for filtering
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
    
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }
}