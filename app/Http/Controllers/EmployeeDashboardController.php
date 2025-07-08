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
        // Get delivery-related data - handle potential missing columns gracefully
        try {
            $assignedDeliveries = \App\Models\Delivery::latest()->take(10)->get();
            $completedDeliveries = \App\Models\Delivery::where('status', 'completed')->count();
            $pendingDeliveries = \App\Models\Delivery::where('status', 'pending')->count();
        } catch (\Exception $e) {
            // Fallback if delivery table doesn't have expected columns
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
}
