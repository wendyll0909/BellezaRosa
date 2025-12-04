<?php
// [file name]: app/Models/InventoryItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name', 'category', 'current_stock', 
        'minimum_stock', 'unit', 'cost', 'supplier'
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'cost' => 'decimal:2'
    ];

    public function updates()
    {
        return $this->hasMany(InventoryUpdate::class, 'item_id');
    }

    public function isLowStock()
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function updateStock($type, $quantity, $remark)
    {
        $previousStock = $this->current_stock;
        
        switch ($type) {
            case 'add':
                $this->current_stock += $quantity;
                break;
            case 'subtract':
                $this->current_stock -= $quantity;
                if ($this->current_stock < 0) $this->current_stock = 0;
                break;
            case 'set':
                $this->current_stock = $quantity;
                break;
        }

        $this->save();

        // Create update record
        InventoryUpdate::create([
            'item_id' => $this->id,
            'type' => $type,
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $this->current_stock,
            'remark' => $remark,
            'updated_by' => auth()->id(),
            'update_date' => now()->toDateString()
        ]);

        return $this;
    }
}   