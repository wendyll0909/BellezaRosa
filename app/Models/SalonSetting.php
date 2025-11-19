<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalonSetting extends Model
{
    protected $primaryKey = 'setting_id';
    public $incrementing = false;
    
    protected $fillable = [
        'setting_id',
        'opening_time',
        'closing_time', 
        'slot_interval_minutes',
        'max_days_book_ahead',
        'cancel_cutoff_hours'
    ];

    protected $attributes = [
        'opening_time' => '09:00:00',
        'closing_time' => '20:00:00',
        'slot_interval_minutes' => 30,
        'max_days_book_ahead' => 60,
        'cancel_cutoff_hours' => 2,
    ];

    public static function getSettings()
    {
        return self::first() ?? self::create(['setting_id' => 1]);
    }
}