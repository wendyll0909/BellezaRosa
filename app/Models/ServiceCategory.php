<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'specialty', // Add this field: 'hair', 'nail', 'both'
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
    
    // Scope for filtering by specialty
    public function scopeBySpecialty($query, $specialty)
    {
        return $query->where('specialty', $specialty)
                    ->orWhere('specialty', 'both');
    }
}