<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalonSetting extends Model
{
protected $primaryKey = 'id';
public $incrementing = false;

public static function getSettings()
{
    return self::find(1) ?? self::create(['id' => 1]);
}}
