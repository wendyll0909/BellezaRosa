<?php
// [file name]: Payment.php
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
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime'
    ];

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

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
    }

    // Scope for filtering
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }
}