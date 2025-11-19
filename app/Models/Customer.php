<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'email',
        'gender',
        'birth_date',
        'notes',
        'total_visits',
        'total_spent',
        'last_visit'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}