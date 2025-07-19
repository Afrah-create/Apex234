<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;
        
        // If user doesn't have an employee record, create one
        if (!$employee) {
            $employee = \App\Models\Employee::create([
                'name' => $user->name,
                'role' => 'Production Worker',
                'user_id' => $user->id,
                'status' => 'active',
            ]);
        }
        
        // Route to role-specific dashboard
        return $this->routeToRoleDashboard($employee);
    }
    
    private function routeToRoleDashboard($employee)
    {
        switch ($employee->role) {
            case 'Production Worker':
                return $this->productionWorkerDashboard($employee);
            case 'Warehouse Staff':
                return $this->warehouseStaffDashboard($employee);
            case 'Driver':
                return $this->driverDashboard($employee);
            case 'Sales Manager':
                return $this->salesManagerDashboard($employee);
            default:
                return view('employee.dashboard', compact('employee'));
        }
    }
    
    public function productionWorkerDashboard($employee)
    {
        // Get production-related data - use facility_id from employee's vendor
        $facilityId = $employee->vendor ? $employee->vendor->id : null;
        
        try {
            $qualityChecks = $facilityId 
                ? \App\Models\QualityCheck::where('facility_id', $facilityId)->latest()->take(5)->get()
                : collect([]);
                
            $yogurtProducts = $facilityId 
                ? \App\Models\YogurtProduct::where('facility_id', $facilityId)->get()
                : collect([]);
                
            $rawMaterials = $facilityId 
                ? \App\Models\RawMaterial::where('facility_id', $facilityId)->get()
                : collect([]);
        } catch (\Exception $e) {
            // Fallback if tables don't exist or have issues
            $qualityChecks = collect([]);
            $yogurtProducts = collect([]);
            $rawMaterials = collect([]);
        }
        
        return view('employee.production-worker.dashboard', compact('employee', 'qualityChecks', 'yogurtProducts', 'rawMaterials'));
    }
    
    public function warehouseStaffDashboard($employee)
    {
        // Get inventory-related data - use facility_id from employee's vendor
        $facilityId = $employee->vendor ? $employee->vendor->id : null;
        
        try {
            $inventory = $facilityId 
                ? \App\Models\Inventory::where('facility_id', $facilityId)->get()
                : collect([]);
                
            $yogurtProducts = $facilityId 
                ? \App\Models\YogurtProduct::where('facility_id', $facilityId)->get()
                : collect([]);
                
            $recentDeliveries = $facilityId 
                ? \App\Models\Delivery::where('facility_id', $facilityId)->latest()->take(5)->get()
                : collect([]);
        } catch (\Exception $e) {
            // Fallback if tables don't exist or have issues
            $inventory = collect([]);
            $yogurtProducts = collect([]);
            $recentDeliveries = collect([]);
        }
        
        return view('employee.warehouse-staff.dashboard', compact('employee', 'inventory', 'yogurtProducts', 'recentDeliveries'));
    }
    
    public function driverDashboard($employee)
    {
        // Get delivery-related data for this driver (employee)
        try {
            $driver = \App\Models\Driver::where('employee_id', $employee->id)->first();
            $assignedDeliveries = $driver
                ? \App\Models\Delivery::where('driver_id', $driver->id)->latest()->take(10)->get()
                : collect([]);
            $completedDeliveries = $driver
                ? \App\Models\Delivery::where('driver_id', $driver->id)->where('delivery_status', 'delivered')->count()
                : 0;
            $pendingDeliveries = $driver
                ? \App\Models\Delivery::where('driver_id', $driver->id)->where('delivery_status', 'scheduled')->count()
                : 0;
        } catch (\Exception $e) {
            $assignedDeliveries = collect([]);
            $completedDeliveries = 0;
            $pendingDeliveries = 0;
        }
        return view('employee.driver.dashboard', compact('employee', 'assignedDeliveries', 'completedDeliveries', 'pendingDeliveries'));
    }
    
    public function salesManagerDashboard($employee)
    {
        // Get sales-related data - use proper relationships instead of vendor_id
        // Since orders don't have vendor_id, we'll get all orders and filter by employee's vendor if needed
        
        try {
            $recentOrders = \App\Models\Order::latest()->take(10)->get();
            $totalOrders = \App\Models\Order::count();
            $pendingOrders = \App\Models\Order::where('order_status', 'pending')->count();
            $completedOrders = \App\Models\Order::where('order_status', 'delivered')->count();
            
            // Calculate sales statistics
            $totalSales = \App\Models\Order::where('payment_status', 'paid')->sum('total_amount');
            $monthlyOrders = \App\Models\Order::whereMonth('created_at', now()->month)->count();
            $activeCustomers = \App\Models\Retailer::count();
            $conversionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;
        } catch (\Exception $e) {
            // Fallback if tables don't exist or have issues
            $recentOrders = collect([]);
            $totalOrders = 0;
            $pendingOrders = 0;
            $completedOrders = 0;
            $totalSales = 0;
            $monthlyOrders = 0;
            $activeCustomers = 0;
            $conversionRate = 0;
        }
        
        // Get top products (placeholder data for now)
        $topProducts = collect([]);
        
        // Customer segments (placeholder data)
        $premiumCustomers = 0;
        $regularCustomers = 0;
        $newCustomers = 0;
        
        // Sales performance (placeholder data)
        $monthlySales = $totalSales;
        $monthlyGrowth = 0;
        $lastMonthSales = 0;
        $monthlyTarget = 10000;
        $targetProgress = $monthlyTarget > 0 ? round(($monthlySales / $monthlyTarget) * 100, 1) : 0;
        
        return view('employee.sales-manager.dashboard', compact(
            'employee', 
            'recentOrders', 
            'totalOrders', 
            'pendingOrders', 
            'completedOrders',
            'totalSales',
            'monthlyOrders',
            'activeCustomers',
            'conversionRate',
            'topProducts',
            'premiumCustomers',
            'regularCustomers',
            'newCustomers',
            'monthlySales',
            'monthlyGrowth',
            'lastMonthSales',
            'monthlyTarget',
            'targetProgress'
        ));
    }

    /**
     * Warehouse staff action: Mark order as packed (processing)
     */
    public function markOrderPacked(Request $request, $orderId)
    {
        $user = Auth::user();
        $employee = $user->employee;
        if (!$employee || $employee->role !== 'Warehouse Staff') {
            return redirect()->back()->with('error', 'Unauthorized: Only warehouse staff can perform this action.');
        }
        $order = \App\Models\Order::findOrFail($orderId);
        if ($order->order_status !== 'confirmed') {
            return redirect()->back()->with('error', 'Order must be confirmed to be marked as packed.');
        }
        $order->order_status = 'processing';
        $order->save();
        // Optionally: notify customer or admin here
        return redirect()->back()->with('success', 'Order marked as packed and is now processing.');
    }

    /**
     * Warehouse staff action: Mark order as shipped (assign driver, update delivery)
     */
    public function markOrderShipped(Request $request, $orderId)
    {
        $user = Auth::user();
        $employee = $user->employee;
        if (!$employee || $employee->role !== 'Warehouse Staff') {
            return redirect()->back()->with('error', 'Unauthorized: Only warehouse staff can perform this action.');
        }
        $order = \App\Models\Order::findOrFail($orderId);
        if ($order->order_status !== 'processing') {
            return redirect()->back()->with('error', 'Order must be processing to be marked as shipped.');
        }
        // Assign the least busy active driver
        $driver = \App\Models\Driver::withCount(['deliveries' => function($query) {
            $query->whereIn('delivery_status', ['scheduled', 'in_transit', 'out_for_delivery']);
        }])->where('status', 'active')->orderBy('deliveries_count', 'asc')->first();
        if ($driver) {
            $order->driver_id = $driver->id;
        }
        $order->order_status = 'shipped';
        $order->save();
        // Update or create delivery record
        $delivery = $order->delivery;
        if (!$delivery) {
            $delivery = $order->delivery()->create([
                'order_id' => $order->id,
                'distribution_center_id' => $order->distribution_center_id,
                'delivery_status' => 'scheduled',
                'delivery_address' => $order->delivery_address,
                'recipient_name' => $order->delivery_contact,
                'recipient_phone' => $order->delivery_phone,
                'delivery_number' => uniqid('DEL-'),
                'driver_id' => $driver ? $driver->id : null,
                'driver_name' => $driver ? $driver->name : null,
                'driver_phone' => $driver ? $driver->phone : null,
                'driver_license' => $driver ? $driver->license : null,
                'vehicle_number' => $driver ? $driver->vehicle_number : null,
                'scheduled_delivery_date' => $order->requested_delivery_date ?? now()->addDay(),
                'scheduled_delivery_time' => '09:00',
            ]);
        } else {
            $delivery->driver_id = $driver ? $driver->id : null;
            $delivery->driver_name = $driver ? $driver->name : null;
            $delivery->driver_phone = $driver ? $driver->phone : null;
            $delivery->driver_license = $driver ? $driver->license : null;
            $delivery->vehicle_number = $driver ? $driver->vehicle_number : null;
            $delivery->delivery_status = 'scheduled';
            $delivery->save();
        }
        // Optionally: notify customer or admin here
        return redirect()->back()->with('success', 'Order marked as shipped and driver assigned.');
    }

    /**
     * Driver action: Mark delivery as out for delivery
     */
    public function markDeliveryOutForDelivery(Request $request, $deliveryId)
    {
        $user = Auth::user();
        $employee = $user->employee;
        if (!$employee || $employee->role !== 'Driver') {
            return redirect()->back()->with('error', 'Unauthorized: Only drivers can perform this action.');
        }
        $delivery = \App\Models\Delivery::findOrFail($deliveryId);
        if ($delivery->delivery_status !== 'scheduled') {
            return redirect()->back()->with('error', 'Delivery must be scheduled to be marked as out for delivery.');
        }
        if ($delivery->driver_id != $employee->id && $delivery->driver_id != $employee->driver->id) {
            return redirect()->back()->with('error', 'You are not assigned to this delivery.');
        }
        $delivery->delivery_status = 'out_for_delivery';
        $delivery->actual_departure_time = now();
        $delivery->save();
        // Optionally: notify customer or admin here
        return redirect()->back()->with('success', 'Delivery marked as out for delivery.');
    }

    /**
     * Driver action: Mark delivery as delivered (with optional proof upload)
     */
    public function markDeliveryDelivered(Request $request, $deliveryId)
    {
        $user = Auth::user();
        $employee = $user->employee;
        if (!$employee || $employee->role !== 'Driver') {
            return redirect()->back()->with('error', 'Unauthorized: Only drivers can perform this action.');
        }
        $delivery = \App\Models\Delivery::findOrFail($deliveryId);
        // Allow marking as delivered from either 'scheduled' or 'out_for_delivery'
        if (!in_array($delivery->delivery_status, ['scheduled', 'out_for_delivery'])) {
            return redirect()->back()->with('error', 'Delivery must be scheduled or out for delivery to be marked as delivered.');
        }
        if ($delivery->driver_id != $employee->id && $delivery->driver_id != $employee->driver->id) {
            return redirect()->back()->with('error', 'You are not assigned to this delivery.');
        }
        // Handle proof upload if provided
        if ($request->hasFile('proof_photo')) {
            $path = $request->file('proof_photo')->store('proof_photos', 'public');
            $delivery->proof_photo = $path;
        }
        $delivery->delivery_status = 'delivered';
        $delivery->actual_delivery_time = now();
        $delivery->save();
        // Optionally: notify customer or admin here
        return redirect()->back()->with('success', 'Delivery marked as delivered.');
    }
}
