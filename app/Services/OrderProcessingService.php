<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\YogurtProduct;
use App\Models\User;
use App\Models\Retailer;
use App\Models\DistributionCenter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderStatusUpdate;

class OrderProcessingService
{
    /**
     * Process a new customer order automatically
     */
    public function processCustomerOrder(Order $order)
    {
        // If bulk order, require admin approval before processing
        if ($order->order_type === 'bulk') {
            $order->update([
                'order_status' => 'pending_admin_approval',
                'notes' => $order->notes . ' [Pending admin approval for bulk order]'
            ]);
            Log::info('Bulk order requires admin approval', ['order_id' => $order->id]);
            return false;
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
            Log::info('Customer order processed successfully', ['order_id' => $order->id]);
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order processing failed', [
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

            // Reserve inventory
            $this->reserveInventory($order);

            // Send confirmation to retailer
            $this->sendRetailerOrderConfirmation($order);

            DB::commit();
            Log::info('Retailer order processed successfully', ['order_id' => $order->id]);
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Retailer order processing failed', [
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
            Log::info('Order status updated', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order status update failed', [
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
        
        foreach ($orderItems as $item) {
            $product = $item->yogurtProduct;
            if (!$product) {
                return [
                    'success' => false,
                    'message' => "Product not found for item {$item->id}"
                ];
            }

            $availableStock = $product->stock ?? 0;
            if ($availableStock < $item->quantity) {
                return [
                    'success' => false,
                    'message' => "Insufficient stock for {$product->product_name}. Available: {$availableStock}, Requested: {$item->quantity}"
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
        foreach ($orderItems as $item) {
            $product = $item->yogurtProduct;
            $vendorId = $product->vendor_id; // Use the product's vendor
            // Deduct from inventory by vendor and distribution center
            $inventory = \App\Models\Inventory::where('yogurt_product_id', $product->id)
                ->where('vendor_id', $vendorId)
                ->when($distributionCenterId, function($query) use ($distributionCenterId) {
                    return $query->where('distribution_center_id', $distributionCenterId);
                })
                ->orderByDesc('quantity_available')
                ->first();
            if ($inventory) {
                $inventory->quantity_available = max(0, $inventory->quantity_available - $item->quantity);
                $inventory->save();
            } else {
                \Log::warning('No inventory record found for deduction', [
                    'product_id' => $product->id,
                    'vendor_id' => $vendorId,
                    'distribution_center_id' => $distributionCenterId,
                    'order_id' => $order->id,
                ]);
            }
        }
    }

    /**
     * Assign order to nearest distribution center
     */
    private function assignDistributionCenter(Order $order)
    {
        // Simple logic - can be enhanced with real distance calculation
        $distributionCenter = DistributionCenter::where('status', 'operational')
            ->orderBy('id')
            ->first();

        if (!$distributionCenter) {
            throw new \Exception('No operational distribution center available');
        }

        return $distributionCenter;
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
    private function sendOrderConfirmation(Order $order)
    {
        try {
            $user = $order->customer;
            $orderItems = $order->orderItems()->with('yogurtProduct')->get();
            
            Mail::send('emails.order-confirmation', [
                'order' => $order,
                'user' => $user,
                'orderItems' => $orderItems
            ], function ($message) use ($user, $order) {
                $message->to($user->email, $user->name)
                        ->subject('Order Confirmed - ' . $order->order_number);
            });
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation', [
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
            Log::error('Failed to send retailer order confirmation', [
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
            Log::error('Failed to send status update notification', [
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
            Log::error('Failed to send order cancellation notification', [
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
            Log::error('Failed to send retailer order pending notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 