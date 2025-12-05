<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportHistory extends Model
{
    // Specify the table name explicitly
    protected $table = 'report_history';

    protected $fillable = [
        'user_id',
        'report_type',
        'start_date',
        'end_date',
        'filename',
        'record_count',
        'parameters'
    ];

    protected $casts = [
        'parameters' => 'array',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to get formatted report type
    public function getFormattedTypeAttribute()
    {
        return ucfirst($this->report_type);
    }

    // Helper method to get date range label
    public function getDateRangeLabelAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_date);
        $end = \Carbon\Carbon::parse($this->end_date);
        
        if ($start->isSameDay($end)) {
            return $start->format('M j, Y');
        }
        
        if ($start->isSameMonth($end)) {
            return $start->format('M j') . ' - ' . $end->format('j, Y');
        }
        
        if ($start->isSameYear($end)) {
            return $start->format('M j') . ' - ' . $end->format('M j, Y');
        }
        
        return $start->format('M j, Y') . ' - ' . $end->format('M j, Y');
    }
}