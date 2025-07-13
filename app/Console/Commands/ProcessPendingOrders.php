<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\OrderProcessingService;
use Illuminate\Support\Facades\Log;

class ProcessPendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:process-pending {--limit=10 : Number of orders to process at once}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending orders automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $orderProcessingService = new OrderProcessingService();

        $this->info("Processing up to {$limit} pending orders...");

        // Get pending customer orders
        $pendingCustomerOrders = Order::where('order_type', 'customer')
            ->where('order_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        $this->info("Found {$pendingCustomerOrders->count()} pending customer orders.");

        $processedCount = 0;
        $failedCount = 0;

        foreach ($pendingCustomerOrders as $order) {
            try {
                $this->line("Processing order {$order->order_number}...");
                
                $result = $orderProcessingService->processCustomerOrder($order);
                
                if ($result) {
                    $this->info("✓ Order {$order->order_number} processed successfully.");
                    $processedCount++;
                } else {
                    $this->error("✗ Order {$order->order_number} processing failed.");
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Error processing order {$order->order_number}: " . $e->getMessage());
                Log::error('Order processing command error', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage()
                ]);
                $failedCount++;
            }
        }

        // Get pending retailer orders
        $pendingRetailerOrders = Order::where('order_type', 'regular')
            ->where('order_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit($limit - $processedCount)
            ->get();

        $this->info("Found {$pendingRetailerOrders->count()} pending retailer orders.");

        foreach ($pendingRetailerOrders as $order) {
            try {
                $this->line("Processing retailer order {$order->order_number}...");
                
                $result = $orderProcessingService->processRetailerOrder($order);
                
                if ($result) {
                    $this->info("✓ Retailer order {$order->order_number} processed successfully.");
                    $processedCount++;
                } else {
                    $this->error("✗ Retailer order {$order->order_number} processing failed.");
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Error processing retailer order {$order->order_number}: " . $e->getMessage());
                Log::error('Retailer order processing command error', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage()
                ]);
                $failedCount++;
            }
        }

        $this->info("Processing completed!");
        $this->info("Successfully processed: {$processedCount} orders");
        $this->info("Failed to process: {$failedCount} orders");

        return 0;
    }
} 