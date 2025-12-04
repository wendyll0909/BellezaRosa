<?php
// [file name]: app/Http/Controllers/InventoryController.php
namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryUpdate;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Main inventory page
public function index(Request $request)
{
    $items = InventoryItem::orderBy('current_stock')->get();
    $lowStockCount = InventoryItem::whereRaw('current_stock <= minimum_stock')->count();
    
    // Get selected date or default to today
    $selectedDate = $request->get('date', today()->toDateString());
    
    // Get updates for selected date
    $todayUpdates = InventoryUpdate::whereDate('update_date', $selectedDate)
        ->with(['item', 'updatedBy'])
        ->orderBy('created_at', 'DESC')
        ->get();

    // Get available dates for calendar (dates that have updates)
    $availableDates = InventoryUpdate::selectRaw('DISTINCT DATE(update_date) as date')
        ->orderBy('date', 'DESC')
        ->limit(30) // Last 30 days
        ->pluck('date');
    
    // Check if we should show all items or just limited
    $showAllItems = $request->get('show_all', false);
    $totalItemsCount = $items->count();
    
    // Limit items to 3 if not showing all
    $limitedItems = $showAllItems ? $items : $items->take(3);

    return view('dashboard.inventory.index', compact(
        'items', 'limitedItems', 'lowStockCount', 'todayUpdates', 
        'selectedDate', 'availableDates', 'totalItemsCount', 'showAllItems'
    ));
}
// Save daily updates with removed items handling
public function saveDailyUpdates(Request $request)
{
    $request->validate([
        'updates' => 'required|array',
        'removed_items' => 'nullable|array'
    ]);

    $updates = $request->updates;
    $removedItemIds = $request->removed_items ?? [];
    $savedCount = 0;
    $errors = [];
    
    $updateDate = now()->toDateString();

    // Start a database transaction
    \DB::beginTransaction();

    try {
        // Process each update - ALWAYS CREATE NEW RECORD
        foreach ($updates as $updateData) {
            $item = InventoryItem::find($updateData['item_id']);
            
            if (!$item) {
                $errors[] = "Item ID {$updateData['item_id']} not found";
                continue;
            }

            // Validate the update
            if ($updateData['type'] === 'subtract' && $updateData['quantity'] > $item->current_stock) {
                $errors[] = "Cannot subtract more than current stock for {$item->name}";
                continue;
            }

            $previousStock = $item->current_stock;
            
            // Apply the update
            switch ($updateData['type']) {
                case 'add':
                    $item->current_stock += $updateData['quantity'];
                    break;
                case 'subtract':
                    $item->current_stock -= $updateData['quantity'];
                    if ($item->current_stock < 0) $item->current_stock = 0;
                    break;
            }

            $item->save();

            // ALWAYS CREATE A NEW RECORD - Never update existing
            InventoryUpdate::create([
                'item_id' => $item->id,
                'type' => $updateData['type'],
                'quantity' => $updateData['quantity'],
                'previous_stock' => $previousStock,
                'new_stock' => $item->current_stock,
                'remark' => $updateData['remark'],
                'updated_by' => auth()->id(),
                'update_date' => $updateDate
            ]);

            $savedCount++;
        }

        // Delete today's updates for removed items
        if (!empty($removedItemIds)) {
            // Get the IDs of updates to delete
            $updatesToDelete = InventoryUpdate::whereIn('item_id', $removedItemIds)
                ->whereDate('update_date', $updateDate)
                ->where('updated_by', auth()->id()) // Only delete current user's updates
                ->pluck('id');
            
            // Delete them
            InventoryUpdate::whereIn('id', $updatesToDelete)->delete();
        }

        if (empty($errors)) {
            \DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Daily updates saved successfully',
                'saved_count' => $savedCount,
                'removed_count' => count($removedItemIds)
            ]);
        } else {
            \DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Some updates failed',
                'errors' => $errors,
                'saved_count' => $savedCount
            ], 422);
        }
        
    } catch (\Exception $e) {
        \DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage(),
            'errors' => ['Database transaction failed']
        ], 500);
    }
}
    // Daily update page
// Daily update page (always for today)
public function dailyUpdate()
{
    $items = InventoryItem::orderBy('name')->get();
    
    // Get updates for today only - Get the LATEST update for each item
    $todayUpdates = InventoryUpdate::whereDate('update_date', today())
        ->with('item')
        ->orderBy('created_at', 'DESC')  // Explicit DESC
        ->get()
        ->groupBy('item_id')
        ->map(function ($updates) {
            return $updates->first(); // Get the LATEST update for each item
        });
    
    return view('dashboard.inventory.daily-update', compact(
        'items', 'todayUpdates'
    ));
}

    // Update stock (AJAX)
    public function updateStock(Request $request, InventoryItem $item)
{
    $request->validate([
        'type' => 'required|in:add,subtract,set',
        'quantity' => 'required|integer|min:1',
        'remark' => 'required|string|min:5'
    ]);

    if ($request->type === 'subtract' && $request->quantity > $item->current_stock) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot subtract more than current stock!'
        ], 422);
    }

    $previousStock = $item->current_stock;
    
    switch ($request->type) {
        case 'add':
            $item->current_stock += $request->quantity;
            break;
        case 'subtract':
            $item->current_stock -= $request->quantity;
            if ($item->current_stock < 0) $item->current_stock = 0;
            break;
        case 'set':
            $item->current_stock = $request->quantity;
            break;
    }

    $item->save();

    // ALWAYS CREATE NEW RECORD
    InventoryUpdate::create([
        'item_id' => $item->id,
        'type' => $request->type,
        'quantity' => $request->quantity,
        'previous_stock' => $previousStock,
        'new_stock' => $item->current_stock,
        'remark' => $request->remark,
        'updated_by' => auth()->id(),
        'update_date' => now()->toDateString()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Stock updated successfully!',
        'new_stock' => $item->current_stock,
        'is_low_stock' => $item->isLowStock()
    ]);
}

    // Add new item
  public function storeItem(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'current_stock' => 'required|integer|min:0',
        'minimum_stock' => 'required|integer|min:0',
        'unit' => 'required|string|max:20'
    ]);

    // Create item with stock set to 0 initially
    $item = InventoryItem::create([
        'name' => $request->name,
        'current_stock' => 0, // Start with 0
        'minimum_stock' => $request->minimum_stock,
        'unit' => $request->unit,
        'category' => $request->category ?? 'hair_care'
    ]);

    // Create initial update record
    if ($request->current_stock > 0) {
        $item->updateStock(
            'add', 
            $request->current_stock,
            'Initial stock entry'
        );
    }

    return back()->with('success', 'Item added successfully!');
}
    // Add this method to InventoryController
public function getItem(InventoryItem $item)
{
    return response()->json([
        'id' => $item->id,
        'name' => $item->name,
        'current_stock' => $item->current_stock,
        'minimum_stock' => $item->minimum_stock,
        'unit' => $item->unit
    ]);
}
}