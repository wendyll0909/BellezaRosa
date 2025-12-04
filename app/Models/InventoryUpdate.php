<?php
// [file name]: app/Models/InventoryUpdate.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryUpdate extends Model
{
    protected $fillable = [
        'item_id', 'type', 'quantity', 'previous_stock',
        'new_stock', 'remark', 'updated_by', 'update_date'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'new_stock' => 'integer',
        'update_date' => 'date'
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}