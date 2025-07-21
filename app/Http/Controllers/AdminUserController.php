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
    
    public function create()
    {
        // Return the view for the user creation form
        return view('admin.users.create');
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = \App\Models\Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,employee,vendor,customer',
            // Optionally: 'workforce_role' => 'nullable|string|in:Warehouse Staff,Driver',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);

            // Sync roles using the role name
            $roleModel = \App\Models\Role::where('name', $request->role)->first();
            if ($roleModel) {
                $user->roles()->sync([$roleModel->id]);
            }

            // Automatically create Employee record if role is employee
            if ($request->role === 'employee') {
                \App\Models\Employee::firstOrCreate([
                    'user_id' => $user->id,
                ], [
                    'name' => $user->name,
                    'role' => 'Warehouse Staff',
                    'status' => 'active',
                    'distribution_center_id' => null, // Always set this field for new employees
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully',
                    'user' => $user
                ]);
            } else {
                return redirect()->route('admin.users.index')->with('success', 'User created successfully');
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create user: ' . $e->getMessage()
                ], 500);
            } else {
                return redirect()->back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
            }
        }
    }
    
    public function update(Request $request, User $user)
    {
        $rules = [];
        if ($request->has('name')) {
            $rules['name'] = 'string|max:255';
        }
        if ($request->has('email')) {
            $rules['email'] = 'string|email|max:255|unique:users,email,' . $user->id;
        }
        if ($request->has('role')) {
            $rules['role'] = 'string|in:admin,employee,vendor,customer';
        }
        if ($request->has('password') && $request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }
        $validated = $request->validate($rules);
        try {
            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }
            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }
            if (isset($validated['role'])) {
                $user->role = $validated['role'];
                $roleModel = \App\Models\Role::where('name', $validated['role'])->first();
                if ($roleModel) {
                    $user->roles()->sync([$roleModel->id]);
                }
            }
            if (isset($validated['password'])) {
                $user->password = bcrypt($validated['password']);
            }
            $user->save();
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }
    
    public function destroy(User $user)
    {
        try {
            // If the user is an employee, delete the corresponding Employee record
            if ($user->role === 'employee' && $user->employee) {
                $user->employee->delete();
            }
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
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
    
    public function destroyEmployee(Employee $employee)
    {
        try {
            $employee->delete();
            return redirect()->route('admin.users.index')->with('success', 'Employee removed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to remove employee: ' . $e->getMessage()]);
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