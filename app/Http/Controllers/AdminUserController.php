<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\DistributionCenter;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        $employees = Employee::with('user')->paginate(10);
        $distributionCenters = DistributionCenter::paginate(10);
        $deliveries = Delivery::with(['driver', 'distributionCenter'])->paginate(10);
        
        return view('admin.admin-dashboard', compact('users', 'employees', 'distributionCenters', 'deliveries'));
    }
    
    // AJAX methods for tab content reloading
    public function getUsersContent()
    {
        $users = User::paginate(10);
        return view('admin.users.partials.users-list', compact('users'))->render();
    }
    
    public function getWorkforceContent()
    {
        $employees = Employee::with('user')->paginate(10);
        return view('admin.users.partials.workforce-list', compact('employees'))->render();
    }
    
    public function getDistributionCentersContent()
    {
        $distributionCenters = DistributionCenter::paginate(10);
        return view('admin.users.partials.distribution-centers-list', compact('distributionCenters'))->render();
    }
    
    public function getDeliveriesContent()
    {
        $deliveries = Delivery::with(['driver', 'distributionCenter'])->paginate(10);
        return view('admin.users.partials.deliveries-list', compact('deliveries'))->render();
    }
    
    // AJAX form submission methods
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,employee,vendor,customer',
        ]);
        
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:admin,employee,vendor,customer',
        ]);
        
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ]);
            
            if ($request->filled('password')) {
                $user->update(['password' => bcrypt($request->password)]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Workforce management methods
    public function storeEmployee(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'hire_date' => 'required|date',
        ]);
        
        try {
            $employee = Employee::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Employee added successfully',
                'employee' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add employee: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroyEmployee(Employee $employee): JsonResponse
    {
        try {
            $employee->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Employee removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove employee: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Distribution center management methods
    public function storeDistributionCenter(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'capacity' => 'required|integer|min:1',
        ]);
        
        try {
            $distributionCenter = DistributionCenter::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Distribution center created successfully',
                'distribution_center' => $distributionCenter
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create distribution center: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroyDistributionCenter(DistributionCenter $distributionCenter): JsonResponse
    {
        try {
            $distributionCenter->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Distribution center deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete distribution center: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Delivery management methods
    public function updateDeliveryStatus(Request $request, Delivery $delivery): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,in_transit,delivered,cancelled',
        ]);
        
        try {
            $delivery->update(['status' => $request->status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Delivery status updated successfully',
                'delivery' => $delivery
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update delivery status: ' . $e->getMessage()
            ], 500);
        }
    }
} 