<?php

namespace App\Observers;

use App\Models\Receiving;

class ReceivingObserver
{
    /**
     * Handle the Receiving "created" event.
     */
    public function created(Receiving $receiving): void
    {
        $this->updateInventory($receiving->master_item_id, $receiving->qty_pack * $receiving->qty_per_pack);
    }

    /**
     * Handle the Receiving "updated" event.
     */
    public function updated(Receiving $receiving): void
    {
        $oldQty = $receiving->getOriginal('qty_pack') * $receiving->getOriginal('qty_per_pack');
        $newQty = $receiving->qty_pack * $receiving->qty_per_pack;
        $diff = $newQty - $oldQty;

        // If item changed, handle both old and new items
        if ($receiving->isDirty('master_item_id')) {
            $this->updateInventory($receiving->getOriginal('master_item_id'), -$oldQty);
            $this->updateInventory($receiving->master_item_id, $newQty);
        } else {
            $this->updateInventory($receiving->master_item_id, $diff);
        }
    }

    /**
     * Handle the Receiving "deleted" event.
     */
    public function deleted(Receiving $receiving): void
    {
        $this->updateInventory($receiving->master_item_id, -($receiving->qty_pack * $receiving->qty_per_pack));
    }

    /**
     * Handle the Receiving "restored" event.
     */
    public function restored(Receiving $receiving): void
    {
        //
    }

    /**
     * Handle the Receiving "force deleted" event.
     */
    public function forceDeleted(Receiving $receiving): void
    {
        //
    }

    private function updateInventory($itemId, $qtyDiff)
    {
        $inventory = \App\Models\Inventory::firstOrCreate(
            ['master_item_id' => $itemId],
            ['quantity' => 0]
        );

        $inventory->quantity += $qtyDiff;
        $inventory->save();
    }
}
