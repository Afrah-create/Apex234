<?php

namespace App\Models;

use App\Services\BatchProductionService;

class InventoryObserver
{
    public function updated(Inventory $inventory)
    {
        // Only trigger if quantity_available just reached zero
        if ($inventory->getOriginal('quantity_available') > 0 && $inventory->quantity_available == 0) {
            $product = $inventory->yogurtProduct;
            $vendorId = $product->vendor_id ?? $inventory->vendor_id ?? null;
            if ($vendorId && $product) {
                // Attempt to produce one batch
                $service = new BatchProductionService();
                $service->produceBatch($vendorId, $product->id, 1);
            }
        }
    }
} 