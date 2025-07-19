<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\YogurtProduct;
use App\Models\User;
use App\Models\Retailer;
use App\Models\DistributionCenter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderStatusUpdate;
use App\Models\Driver;

class OrderProcessingService
{
    /**
     * Process a new customer order automatically
     */
    public function processCustomerOrder(Order $order)
    {
        if ($order->order_type === 'bulk') {
            DB::beginTransaction();
            try {
                $orderItems = $order->orderItems()->with('yogurtProduct')->get();
                $partiallyFulfilled = [];
                $anyPartial = false;
                foreach ($orderItems as $item) {
                    $product = $item->yogurtProduct;
                    if (!$product) continue;
                    $distributionCenterId = $order->distribution_center_id;
                    $vendorId = $product->vendor_id;
                    $inventory = \App\Models\Inventory::where('yogurt_product_id', $product->id)
                        ->where('vendor_id', $vendorId)
                        ->when($distributionCenterId, function($query) use ($distributionCenterId) {
                            return $query->where('distribution_center_id', $distributionCenterId);
                        })
                        ->orderByDesc('quantity_available')
                        ->first();
                    $available = $inventory ? $inventory->quantity_available : 0;
                    $toFulfill = min($item->quantity, $available);
                    if ($toFulfill < $item->quantity) {
                        $anyPartial = true;
                        $partiallyFulfilled[] = [
                            'product_name' => $product->product_name,
                            'requested' => $item->quantity,
                            'fulfilled' => $toFulfill
                        ];
                    }
                    // Update order item to actual fulfilled quantity
                    $item->fulfilled_quantity = $toFulfill;
                    $item->save();
                    // Deduct inventory
                    if ($inventory) {
                        $inventory->quantity_available = max(0, $inventory->quantity_available - $toFulfill);
                        $inventory->save();
                    }
                }
                $order->update([
                    'order_status' => 'confirmed',
                    'notes' => $order->notes . ($anyPartial ? ' [Partially fulfilled: some items were only partially fulfilled due to limited inventory]' : ' [Auto-confirmed by system]')
                ]);
                if (!$order->distribution_center_id) {
                    $distributionCenter = $this->assignDistributionCenter($order);
                    $order->update(['distribution_center_id' => $distributionCenter->id]);
                }
                // Always create and assign delivery after confirmation
                $this->createAndAssignDelivery($order);
                $this->sendOrderConfirmation($order, $partiallyFulfilled);
                DB::commit();
                \Illuminate\Support\Facades\Log::info('Customer bulk order processed with partial fulfillment', ['order_id' => $order->id]);
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                \Illuminate\Support\Facades\Log::error('Bulk order processing failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                return false;
            }
        }

        DB::beginTransaction();
        try {
            // Validate inventory availability
            $inventoryCheck = $this->validateInventory($order);
            if (!$inventoryCheck['success']) {
                $order->update([
                    'order_status' => 'cancelled',
                    'notes' => $order->notes . ' [Cancelled: ' . $inventoryCheck['message'] . ']'
                ]);
                
                $this->notifyOrderCancellation($order, $inventoryCheck['message']);
                DB::commit();
                return false;
            }

            // Auto-confirm order if inventory is available
            $order->update([
                'order_status' => 'confirmed',
                'notes' => $order->notes . ' [Auto-confirmed by system]'
            ]);

            // Always create and assign delivery after confirmation
            $this->createAndAssignDelivery($order);

            // Reserve inventory
            $this->reserveInventory($order);

            // Assign to nearest distribution center if not already assigned
            if (!$order->distribution_center_id) {
                $distributionCenter = $this->assignDistributionCenter($order);
                $order->update(['distribution_center_id' => $distributionCenter->id]);
            }

            // Send confirmation notifications
            $this->sendOrderConfirmation($order);

            DB::commit();
            \Illuminate\Support\Facades\Log::info('Customer order processed successfully', ['order_id' => $order->id]);
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Order processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Process a new retailer order
     */
    public function processRetailerOrder(Order $order)
    {
        DB::beginTransaction();
        try {
            // Validate inventory for retailer orders
            $inventoryCheck = $this->validateInventory($order);
            if (!$inventoryCheck['success']) {
                $order->update([
                    'order_status' => 'pending',
                    'notes' => $order->notes . ' [Pending: ' . $inventoryCheck['message'] . ']'
                ]);
                
                $this->notifyRetailerOrderPending($order, $inventoryCheck['message']);
                DB::commit();
                return false;
            }

            // Auto-confirm retailer order
            $order->update([
                'order_status' => 'confirmed',
                'notes' => $order->notes . ' [Auto-confirmed for retailer]'
            ]);

            // Always create and assign delivery after confirmation
            $this->createAndAssignDelivery($order);

            // Reserve inventory
            $this->reserveInventory($order);

            // Send confirmation to retailer
            $this->sendRetailerOrderConfirmation($order);

            DB::commit();
            \Illuminate\Support\Facades\Log::info('Retailer order processed successfully', ['order_id' => $order->id]);
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Retailer order processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Update order status with proper validation and notifications
     */
    public function updateOrderStatus(Order $order, string $newStatus, string $notes = null)
    {
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'cancelled'],
            'delivered' => [],
            'cancelled' => []
        ];

        $currentStatus = $order->order_status;
        if (!in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
            throw new \Exception("Invalid status transition from {$currentStatus} to {$newStatus}");
        }

        DB::beginTransaction();
        try {
            $oldStatus = $order->order_status;
            
            // Update order status
            $order->update([
                'order_status' => $newStatus,
                'notes' => $order->notes . ' [' . ($notes ?: "Status changed from {$oldStatus} to {$newStatus}") . ']'
            ]);

            // Handle status-specific actions
            switch ($newStatus) {
                case 'processing':
                    $this->handleProcessingStatus($order);
                    break;
                case 'shipped':
                    $this->handleShippedStatus($order);
                    break;
                case 'delivered':
                    $this->handleDeliveredStatus($order);
                    break;
                case 'cancelled':
                    $this->handleCancelledStatus($order);
                    break;
            }

            // Send notifications
            $this->sendStatusUpdateNotification($order, $oldStatus, $newStatus);

            DB::commit();
            \Illuminate\Support\Facades\Log::info('Order status updated', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Order status update failed', [
                'order_id' => $order->id,
                'new_status' => $newStatus,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Validate inventory availability for order
     */
    private function validateInventory(Order $order)
    {
        $orderItems = $order->orderItems()->with('yogurtProduct')->get();
        $distributionCenterId = $order->distribution_center_id;
        foreach ($orderItems as $item) {
            $product = $item->yogurtProduct;
            if (!$product) {
                return [
                    'success' => false,
                    'message' => "Product not found for item {$item->id}"
                ];
            }
            // Check available stock (available - reserved)
            $availableStock = \App\Models\Inventory::where('yogurt_product_id', $product->id)
                ->where('distribution_center_id', $distributionCenterId)
                ->get()
                ->sum(function($inventory) {
                    return $inventory->quantity_available - $inventory->quantity_reserved;
                });
            if ($availableStock < $item->quantity) {
                return [
                    'success' => false,
                    'message' => "Insufficient stock for {$product->product_name} at the selected distribution center. Available: {$availableStock}, Requested: {$item->quantity}"
                ];
            }
        }
        return ['success' => true];
    }

    /**
     * Reserve inventory for order
     */
    private function reserveInventory(Order $order)
    {
        $orderItems = $order->orderItems()->with('yogurtProduct')->get();
        $distributionCenterId = $order->distribution_center_id;
        
        \Illuminate\Support\Facades\Log::info('Starting inventory reservation', [
            'order_id' => $order->id,
            'distribution_center_id' => $distributionCenterId,
            'order_items_count' => $orderItems->count()
        ]);
        
        foreach ($orderItems as $item) {
            $product = $item->yogurtProduct;
            $quantityToReserve = $item->quantity;
            
            \Illuminate\Support\Facades\Log::info('Processing order item for inventory reservation', [
                'order_id' => $order->id,
                'item_id' => $item->id,
                'product_id' => $product ? $product->id : null,
                'product_name' => $product ? $product->product_name : null,
                'quantity_to_reserve' => $quantityToReserve,
                'distribution_center_id' => $distributionCenterId
            ]);
            
            // Reserve from any available inventory at the center, regardless of vendor
            $inventories = \App\Models\Inventory::where('yogurt_product_id', $product->id)
                ->where('distribution_center_id', $distributionCenterId)
                ->whereRaw('quantity_available > quantity_reserved') // Only inventory that has available stock
                ->orderBy('expiry_date')
                ->get();
                
            \Illuminate\Support\Facades\Log::info('Found inventories for reservation', [
                'order_id' => $order->id,
                'item_id' => $item->id,
                'inventories_count' => $inventories->count(),
                'total_available_before' => $inventories->sum(function($inv) {
                    return $inv->quantity_available - $inv->quantity_reserved;
                })
            ]);
            
            foreach ($inventories as $inventory) {
                if ($quantityToReserve <= 0) break;
                
                $availableForReservation = $inventory->quantity_available - $inventory->quantity_reserved;
                $reserve = min($availableForReservation, $quantityToReserve);
                
                $inventory->quantity_reserved += $reserve;
                
                \Illuminate\Support\Facades\Log::info('Reserving inventory', [
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'inventory_id' => $inventory->id,
                    'reserved_amount' => $reserve,
                    'new_reserved_quantity' => $inventory->quantity_reserved,
                    'available_after_reservation' => $inventory->quantity_available - $inventory->quantity_reserved,
                    'quantity_to_reserve_remaining' => $quantityToReserve
                ]);
                
                // Update inventory_status if needed
                $availableAfterReservation = $inventory->quantity_available - $inventory->quantity_reserved;
                if ($availableAfterReservation === 0) {
                    $inventory->inventory_status = 'out_of_stock';
                } elseif ($availableAfterReservation < 10) {
                    $inventory->inventory_status = 'low_stock';
                } else {
                    $inventory->inventory_status = 'available';
                }
                $inventory->save();
                $quantityToReserve -= $reserve;
            }
            
            // Sync product stock with sum of all available inventory (available - reserved)
            $product->stock = $product->inventories()->get()->sum(function($inv) {
                return $inv->quantity_available - $inv->quantity_reserved;
            });
            $product->save();
            
            \Illuminate\Support\Facades\Log::info('Completed inventory reservation for item', [
                'order_id' => $order->id,
                'item_id' => $item->id,
                'product_id' => $product->id,
                'new_product_stock' => $product->stock,
                'quantity_reserved' => $item->quantity - $quantityToReserve
            ]);
        }
        
        \Illuminate\Support\Facades\Log::info('Completed inventory reservation for order', [
            'order_id' => $order->id
        ]);
    }

    /**
     * Assign order to nearest distribution center
     */
    private function assignDistributionCenter(Order $order)
    {
        // Get all operational distribution centers
        $distributionCenters = \App\Models\DistributionCenter::where('status', 'operational')->get();
        $orderItems = $order->orderItems()->with('yogurtProduct')->get();

        $bestCenter = null;
        foreach ($distributionCenters as $center) {
            $canFulfill = true;
            foreach ($orderItems as $item) {
                $product = $item->yogurtProduct;
                $vendorId = $product->vendor_id;
                $totalAvailable = \App\Models\Inventory::where('yogurt_product_id', $product->id)
                    ->where('vendor_id', $vendorId)
                    ->where('distribution_center_id', $center->id)
                    ->get()
                    ->sum(function($inventory) {
                        return $inventory->quantity_available - $inventory->quantity_reserved;
                    });
                if ($totalAvailable < $item->quantity) {
                    $canFulfill = false;
                    break;
                }
            }
            if ($canFulfill) {
                $bestCenter = $center;
                break; // Pick the first center that can fulfill all items
            }
        }

        if (!$bestCenter) {
            throw new \Exception('No distribution center can fully fulfill this order.');
        }

        return $bestCenter;
    }

    /**
     * Assign a random available driver to the order
     */
    private function assignRandomDriver(Order $order)
    {
        $employeeDriver = \App\Models\Employee::where('role', 'Driver')
            ->where('status', 'Active')
            ->orderByRaw('(
                SELECT COUNT(*) FROM deliveries WHERE deliveries.driver_id = employees.id AND deliveries.delivery_status IN ("scheduled", "in_transit", "out_for_delivery")
            ) ASC')
            ->first();
        if ($employeeDriver) {
            // Assign to order if needed
            $order->driver_id = $employeeDriver->id;
            $order->order_status = 'out_for_delivery';
            $order->save();
            // Create delivery if not exists
            if (!$order->delivery) {
                $order->delivery()->create([
                    'order_id' => $order->id,
                    'distribution_center_id' => $order->distribution_center_id,
                    'vendor_id' => $order->vendor_id,
                    'vehicle_number' => null,
                    'driver_id' => $employeeDriver->id,
                    'driver_name' => $employeeDriver->name,
                    'driver_phone' => $employeeDriver->user ? $employeeDriver->user->mobile ?? $employeeDriver->user->phone ?? null : null,
                    'driver_license' => null, // Add if available in Employee
                    'scheduled_delivery_date' => $order->requested_delivery_date ?? now()->addDay(),
                    'scheduled_delivery_time' => '09:00',
                    'delivery_address' => $order->delivery_address,
                    'recipient_name' => $order->delivery_contact,
                    'recipient_phone' => $order->delivery_phone,
                    'delivery_number' => uniqid('DEL-'),
                    'delivery_status' => 'scheduled',
                ]);
            }
            return true;
        }
        return false;
    }

    /**
     * Handle processing status
     */
    private function handleProcessingStatus(Order $order)
    {
        // Create delivery record
        $order->delivery()->create([
            'order_id' => $order->id,
            'delivery_date' => $order->requested_delivery_date,
            'status' => 'scheduled',
            'driver_id' => null, // Will be assigned later
            'vehicle_id' => null,
            'notes' => 'Order processing started'
        ]);
    }

    /**
     * Handle shipped status
     */
    private function handleShippedStatus(Order $order)
    {
        if ($order->delivery) {
            $order->delivery->update([
                'status' => 'in_transit',
                'actual_departure_time' => now()
            ]);
        }
    }

    /**
     * Handle delivered status
     */
    private function handleDeliveredStatus(Order $order)
    {
        $order->update(['actual_delivery_date' => now()]);
        
        if ($order->delivery) {
            $order->delivery->update([
                'status' => 'delivered',
                'actual_delivery_time' => now()
            ]);
        }

        // Update payment status if cash on delivery
        if ($order->payment_method === 'cash') {
            $order->update(['payment_status' => 'paid']);
        }
    }

    /**
     * Handle cancelled status
     */
    private function handleCancelledStatus(Order $order)
    {
        // Restore inventory
        $orderItems = $order->orderItems()->with('yogurtProduct')->get();
        
        foreach ($orderItems as $item) {
            $product = $item->yogurtProduct;
            $product->stock += $item->quantity;
            $product->save();
        }
    }

    /**
     * Send order confirmation
     */
    private function sendOrderConfirmation(Order $order, $partiallyFulfilled = [])
    {
        try {
            $user = $order->customer;
            $orderItems = $order->orderItems()->with('yogurtProduct')->get();
            Mail::send('emails.order-confirmation', [
                'order' => $order,
                'user' => $user,
                'orderItems' => $orderItems,
                'partiallyFulfilled' => $partiallyFulfilled
            ], function ($message) use ($user, $order) {
                $message->to($user->email, $user->name)
                        ->subject('Order Confirmed - ' . $order->order_number);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send order confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send retailer order confirmation
     */
    private function sendRetailerOrderConfirmation(Order $order)
    {
        try {
            $retailer = $order->retailer;
            if ($retailer && $retailer->user) {
                $orderItems = $order->orderItems()->with('yogurtProduct')->get();
                
                Mail::send('emails.retailer-order-confirmation', [
                    'order' => $order,
                    'retailer' => $retailer,
                    'orderItems' => $orderItems
                ], function ($message) use ($retailer, $order) {
                    $message->to($retailer->user->email, $retailer->user->name)
                            ->subject('Retailer Order Confirmed - ' . $order->order_number);
                });
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send retailer order confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send status update notification
     */
    private function sendStatusUpdateNotification(Order $order, string $oldStatus, string $newStatus)
    {
        try {
            $user = $order->customer;
            if ($user) {
                $user->notify(new OrderStatusUpdate($order, $oldStatus, $newStatus));
            }

            // Also notify retailer if it's a retailer order
            if ($order->retailer && $order->retailer->user) {
                $order->retailer->user->notify(new OrderStatusUpdate($order, $oldStatus, $newStatus));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send status update notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify order cancellation
     */
    private function notifyOrderCancellation(Order $order, string $reason)
    {
        try {
            $user = $order->customer;
            if ($user) {
                Mail::send('emails.order-cancellation', [
                    'order' => $order,
                    'user' => $user,
                    'reason' => $reason
                ], function ($message) use ($user, $order) {
                    $message->to($user->email, $user->name)
                            ->subject('Order Cancelled - ' . $order->order_number);
                });
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send order cancellation notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify retailer about pending order
     */
    private function notifyRetailerOrderPending(Order $order, string $reason)
    {
        try {
            $retailer = $order->retailer;
            if ($retailer && $retailer->user) {
                Mail::send('emails.retailer-order-pending', [
                    'order' => $order,
                    'retailer' => $retailer,
                    'reason' => $reason
                ], function ($message) use ($retailer, $order) {
                    $message->to($retailer->user->email, $retailer->user->name)
                            ->subject('Order Pending - ' . $order->order_number);
                });
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send retailer order pending notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create and assign a delivery to an order using an available driver from employees table
     */
    private function createAndAssignDelivery(Order $order)
    {
        // Only create if not already exists
        if ($order->delivery) {
            return;
        }
        $employeeDriver = \App\Models\Employee::where('role', 'Driver')
            ->where('status', 'Active')
            ->orderByRaw('(
                SELECT COUNT(*) FROM deliveries WHERE deliveries.driver_id = employees.id AND deliveries.delivery_status IN ("scheduled", "in_transit", "out_for_delivery")
            ) ASC')
            ->first();
        if ($employeeDriver) {
            $order->driver_id = $employeeDriver->id;
            $order->order_status = 'out_for_delivery';
            $order->save();
            $order->delivery()->create([
                'order_id' => $order->id,
                'distribution_center_id' => $order->distribution_center_id,
                'vendor_id' => $order->vendor_id,
                'vehicle_number' => null,
                'driver_id' => $employeeDriver->id,
                'driver_name' => $employeeDriver->name,
                'driver_phone' => $employeeDriver->user ? $employeeDriver->user->mobile ?? $employeeDriver->user->phone ?? null : null,
                'driver_license' => null, // Add if available in Employee
                'scheduled_delivery_date' => $order->requested_delivery_date ?? now()->addDay(),
                'scheduled_delivery_time' => '09:00',
                'delivery_address' => $order->delivery_address,
                'recipient_name' => $order->delivery_contact,
                'recipient_phone' => $order->delivery_phone,
                'delivery_number' => uniqid('DEL-'),
                'delivery_status' => 'scheduled',
            ]);
        } else {
            // Optionally, log or notify that no driver is available
            \Illuminate\Support\Facades\Log::warning('No available driver found for delivery assignment', ['order_id' => $order->id]);
        }
    }
} 