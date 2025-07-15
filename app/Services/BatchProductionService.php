<?php

namespace App\Services;

use App\Models\YogurtProduct;
use App\Models\RawMaterial;
use App\Models\ProductionBatch;
use App\Models\Inventory;
use App\Models\DistributionCenter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BatchProductionService
{
    /**
     * Attempt to produce a batch for a vendor and product.
     *
     * @param int $vendorId
     * @param int $productId
     * @param int $batches
     * @return array [success => bool, message => string]
     */
    public function produceBatch($vendorId, $productId, $batches = 1)
    {
        $product = YogurtProduct::find($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }
        $unitsPerBatch = 10;
        $totalUnits = $batches * $unitsPerBatch;
        $requirements = $product->rawMaterialRequirements();
        $requiredMaterials = [];
        foreach ($requirements as $type => $qtyPerBatch) {
            $requiredMaterials[$type] = $qtyPerBatch * $batches;
        }
        $materials = RawMaterial::where('vendor_id', $vendorId)
            ->where('status', 'available')
            ->get()
            ->keyBy('material_type');
        foreach ($requiredMaterials as $type => $requiredQty) {
            if (!isset($materials[$type]) || $materials[$type]->quantity < $requiredQty) {
                return [
                    'success' => false,
                    'message' => "Not enough $type in inventory. Required: $requiredQty, Available: " . ($materials[$type]->quantity ?? 0)
                ];
            }
        }
        DB::transaction(function () use ($vendorId, $product, $batches, $totalUnits, $requiredMaterials, $materials) {
            // Deduct raw materials
            foreach ($requiredMaterials as $type => $requiredQty) {
                $material = $materials[$type];
                $material->quantity -= $requiredQty;
                $material->save();
            }
            // Create batch
            $batch = ProductionBatch::create([
                'vendor_id' => $vendorId,
                'product_id' => $product->id,
                'quantity_produced' => $totalUnits,
                'batch_code' => strtoupper(Str::random(8)),
            ]);
            // Attach raw materials used
            foreach ($requiredMaterials as $type => $qty) {
                $material = $materials[$type];
                $batch->rawMaterials()->attach($material->id, ['quantity_used' => $qty]);
            }
            // Add to inventory
            Inventory::create([
                'yogurt_product_id' => $product->id,
                'distribution_center_id' => DistributionCenter::first()->id,
                'batch_number' => $batch->batch_code,
                'quantity_available' => $totalUnits,
                'quantity_reserved' => 0,
                'quantity_damaged' => 0,
                'quantity_expired' => 0,
                'production_date' => now()->toDateString(),
                'expiry_date' => now()->addDays(30)->toDateString(),
                'storage_temperature' => 4.0,
                'storage_location' => 'refrigerator',
                'shelf_location' => null,
                'inventory_status' => 'available',
                'unit_cost' => $product->production_cost ?? 0,
                'total_value' => ($product->production_cost ?? 0) * $totalUnits,
                'last_updated' => now()->toDateString(),
                'notes' => 'Batch created from production',
            ]);
        });
        return ['success' => true, 'message' => 'Batch produced successfully.'];
    }
} 