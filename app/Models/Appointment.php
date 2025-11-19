<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'customer_id',
        'staff_id',
        'service_id',
        'start_datetime',
        'end_datetime',
        'status',
        'payment_method',
        'total_amount',
        'notes',
        'is_walk_in'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_walk_in' => 'boolean',
        'total_amount' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function addons()
    {
        return $this->hasMany(AppointmentAddon::class);
    }
}