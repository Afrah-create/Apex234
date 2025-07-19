<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\Delivery;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $priority = ['admin', 'supplier', 'vendor', 'retailer'];
        $query = User::with('roles');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $query->get()->sortBy(function($user) use ($priority) {
            $userRoles = $user->roles->pluck('name')->toArray();
            foreach ($priority as $index => $role) {
                if (in_array($role, $userRoles)) {
                    return $index;
                }
            }
            return count($priority);
        })->values();
        $perPage = 10;
        $page = $request->input('page', 1);
        $paginatedUsers = new \Illuminate\Pagination\LengthAwarePaginator(
            $users->forPage($page, $perPage),
            $users->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $employees = Employee::all();
        $vendors = Vendor::where('status', 'approved')->get();
        $vendorApplicants = \App\Models\VendorApplicant::whereIn('status', ['validated', 'pending'])->get();
        $deliveries = \App\Models\Delivery::with(['order.customer', 'order.orderItems.yogurtProduct', 'retailer'])
            ->latest()
            ->get();
        return view('admin.users.index', ['users' => $paginatedUsers, 'employees' => $employees, 'vendors' => $vendors, 'vendorApplicants' => $vendorApplicants, 'deliveries' => $deliveries]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'mobile' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'photo_url' => 'nullable|url',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);
        return Redirect::route('admin.users.index')->with('status', 'User created!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = \App\Models\Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'photo_url' => 'nullable|url',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);
        if ($request->has('role')) {
            $roleId = $request->input('role');
            $user->roles()->sync([$roleId]);

            // Get the role name
            $roleName = \App\Models\Role::find($roleId)->name;

            // Auto-create profile for the assigned role if missing
            if ($roleName === 'supplier' && !$user->supplier) {
                \App\Models\Supplier::create([
                    'user_id' => $user->id,
                    'company_name' => $user->name . " Company",
                    'registration_number' => 'REG-' . strtoupper(uniqid()),
                    'business_address' => 'TBD',
                    'contact_person' => $user->name,
                    'contact_phone' => 'TBD',
                    'contact_email' => $user->email,
                    'supplier_type' => 'dairy_farm',
                    'status' => 'pending',
                    'rating' => 0,
                    'certifications' => json_encode([]),
                    'verification_date' => null,
                    'contract_start_date' => null,
                    'contract_end_date' => null,
                    'credit_limit' => 0,
                    'payment_terms_days' => 30,
                    'notes' => null,
                ]);
            } elseif ($roleName === 'vendor' && !$user->vendor) {
                \App\Models\Vendor::create([
                    'user_id' => $user->id,
                ]);
            } elseif ($roleName === 'retailer' && !$user->retailer) {
                \App\Models\Retailer::create([
                    'user_id' => $user->id,
                    'store_name' => $user->name . " Store",
                    'store_code' => 'STORE-' . strtoupper(uniqid()),
                    'store_address' => 'TBD',
                    'store_phone' => 'TBD',
                    'store_email' => $user->email,
                    'store_manager' => $user->name,
                    'manager_phone' => 'TBD',
                    'manager_email' => $user->email,
                    'store_type' => 'supermarket',
                    'store_size' => 'small',
                    'daily_customer_traffic' => null,
                    'monthly_sales_volume' => null,
                    'payment_methods' => json_encode([]),
                    'store_hours' => json_encode([]),
                    'certification_status' => 'pending',
                    'certifications' => json_encode([]),
                    'last_inspection_date' => null,
                    'next_inspection_date' => null,
                    'customer_rating' => 0,
                    'status' => 'active',
                    'notes' => null,
                ]);
            } elseif ($roleName === 'employee' && !$user->employee) {
                \App\Models\Employee::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'role' => 'Production Worker',
                    'status' => 'active',
                ]);
            }
        } else {
            $user->roles()->detach();
        }

        // If the updated user is the currently logged-in user, redirect to their new dashboard
        if (auth()->id() === $user->id) {
            switch ($roleName ?? $user->getPrimaryRoleName()) {
                case 'supplier':
                    return redirect()->route('dashboard.supplier')->with('status', 'User updated!');
                case 'vendor':
                    return redirect()->route('vendor.dashboard')->with('status', 'User updated!');
                case 'retailer':
                    return redirect()->route('retailer.dashboard')->with('status', 'User updated!');
                case 'employee':
                    return redirect()->route('employee.dashboard')->with('status', 'User updated!');
                case 'admin':
                    return redirect()->route('dashboard')->with('status', 'User updated!');
                default:
                    return redirect()->route('dashboard')->with('status', 'User updated!');
            }
        }

        return Redirect::route('admin.users.index')->with('status', 'User updated!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return Redirect::route('admin.users.index')->with('status', 'User deleted!');
    }

    public function loginAs($id)
    {
        $user = User::findOrFail($id);
        Auth::login($user);
        return redirect('/dashboard');
    }
} 