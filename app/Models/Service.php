<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'duration_minutes', 
        'price_regular',
        'price_premium',
        'is_premium',
        'description',
        'consumables',
        'is_active'
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
        'consumables' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function matchesSpecialty($specialty)
{
    return $this->category->specialty === $specialty || 
           $this->category->specialty === 'both';
}
}