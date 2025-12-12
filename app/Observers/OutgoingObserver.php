<?php

namespace App\Observers;

use App\Models\Outgoing;

use App\Models\Inventory;

class OutgoingObserver
{
    /**
     * Handle the Outgoing "created" event.
     */
    public function created(Outgoing $outgoing): void
    {
        $inventory = Inventory::where('master_item_id', $outgoing->master_item_id)->first();

        if ($inventory) {
            $oldQty = $inventory->quantity;
            $inventory->quantity -= $outgoing->quantity;
            $inventory->save();

            \App\Services\ActivityLogService::log(
                'update',
                'inventories',
                $inventory->id,
                ['quantity' => $oldQty],
                ['quantity' => $inventory->quantity, 'reason' => 'Outgoing Transaction']
            );
        }
    }

    /**
     * Handle the Outgoing "updated" event.
     */
    public function updated(Outgoing $outgoing): void
    {
        // Handle quantity changes or item changes if needed
        // For simplicity, revert old and apply new
        if ($outgoing->isDirty('quantity') || $outgoing->isDirty('master_item_id')) {
            $originalQuantity = $outgoing->getOriginal('quantity');
            $originalItemId = $outgoing->getOriginal('master_item_id');

            // Revert old
            $oldInventory = Inventory::where('master_item_id', $originalItemId)->first();
            if ($oldInventory) {
                $prevQty = $oldInventory->quantity;
                $oldInventory->quantity += $originalQuantity;
                $oldInventory->save();

                \App\Services\ActivityLogService::log(
                    'update',
                    'inventories',
                    $oldInventory->id,
                    ['quantity' => $prevQty],
                    ['quantity' => $oldInventory->quantity, 'reason' => 'Outgoing Transaction (Update Revert)']
                );
            }

            // Apply new
            $newInventory = Inventory::where('master_item_id', $outgoing->master_item_id)->first();
            if ($newInventory) {
                $prevQty = $newInventory->quantity;
                $newInventory->quantity -= $outgoing->quantity;
                $newInventory->save();

                \App\Services\ActivityLogService::log(
                    'update',
                    'inventories',
                    $newInventory->id,
                    ['quantity' => $prevQty],
                    ['quantity' => $newInventory->quantity, 'reason' => 'Outgoing Transaction (Update New)']
                );
            }
        }
    }

    /**
     * Handle the Outgoing "deleted" event.
     */
    public function deleted(Outgoing $outgoing): void
    {
        $inventory = Inventory::where('master_item_id', $outgoing->master_item_id)->first();

        if ($inventory) {
            $oldQty = $inventory->quantity;
            $inventory->quantity += $outgoing->quantity;
            $inventory->save();

            \App\Services\ActivityLogService::log(
                'update',
                'inventories',
                $inventory->id,
                ['quantity' => $oldQty],
                ['quantity' => $inventory->quantity, 'reason' => 'Outgoing Transaction (Deleted)']
            );
        }
    }

    /**
     * Handle the Outgoing "restored" event.
     */
    public function restored(Outgoing $outgoing): void
    {
        //
    }

    /**
     * Handle the Outgoing "force deleted" event.
     */
    public function forceDeleted(Outgoing $outgoing): void
    {
        //
    }
}
