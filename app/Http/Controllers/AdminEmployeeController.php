<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Delivery;

class AdminEmployeeController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        $employees = Employee::with(['vendor', 'distributionCenter'])->get();
        $vendors = Vendor::where('status', 'approved')->get();
        $distributionCenters = \App\Models\DistributionCenter::orderBy('center_name')->paginate(15);
        $vendorApplicants = \App\Models\VendorApplicant::whereIn('status', ['validated', 'pending'])->get();
        $deliveries = \App\Models\Delivery::with(['order.customer', 'order.orderItems.yogurtProduct', 'retailer'])->latest()->take(50)->get();
        return view('admin.users.index', compact('users', 'employees', 'vendors', 'distributionCenters', 'vendorApplicants', 'deliveries'));
    }

    public function assignVendor(Request $request, Employee $employee)
    {
        $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'distribution_center_id' => 'nullable|exists:distribution_centers,id',
        ]);
        $employee->vendor_id = $request->vendor_id;
        $employee->distribution_center_id = $request->distribution_center_id;
        $employee->save();

        // Notify the employee's user (if exists)
        if ($employee->user_id && $employee->vendor_id) {
            $user = \App\Models\User::find($employee->user_id);
            $vendor = Vendor::find($employee->vendor_id);
            if ($user && $vendor) {
                try {
                    $user->notify(new \App\Notifications\EmployeeAssignedToVendor($employee->role, $vendor));
                } catch (\Exception $e) {
                    // Log the error but don't break the flow
                    Log::error('Failed to send notification: ' . $e->getMessage());
                    return back()->with('warning', 'Vendor assignment updated, but notification failed to send.');
                }
            }
        }

        return back()->with('success', 'Assignment updated!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required',
            'vendor_id' => 'nullable|exists:vendors,id',
            'distribution_center_id' => 'nullable|exists:distribution_centers,id',
            'status' => 'required|in:Active,On Leave,Terminated',
            // Driver fields are optional
            'license' => 'nullable|string|max:255',
            'license_expiry' => 'nullable|date',
            'vehicle_number' => 'nullable|string|max:255',
            'driver_photo' => 'nullable|file|image|max:2048',
        ]);

        // Create the user
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        // Always assign the 'employee' system role
        $employeeRole = \App\Models\Role::where('name', 'employee')->first();
        if ($employeeRole) {
            $user->roles()->sync([$employeeRole->id]);
        }

        // Create the employee record
        $employee = \App\Models\Employee::create([
            'name' => $request->name,
            'role' => $request->role,
            'vendor_id' => $request->vendor_id,
            'distribution_center_id' => $request->distribution_center_id,
            'status' => $request->status,
            'user_id' => $user->id,
        ]);

        // If the role is driver, create a Driver record and link it
        if (strtolower($request->role) === 'driver') {
            $photoPath = null;
            if ($request->hasFile('driver_photo')) {
                $photoPath = $request->file('driver_photo')->store('driver_photos', 'public');
            }
            \App\Models\Driver::create([
                'employee_id' => $employee->id,
                'name' => $request->name,
                'phone' => $request->mobile,
                'email' => $request->email,
                'license' => $request->license,
                'license_expiry' => $request->license_expiry,
                'vehicle_number' => $request->vehicle_number,
                'photo' => $photoPath,
                'status' => 'active',
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Employee created!');
    }

    public function edit($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $vendors = \App\Models\Vendor::all();
        $distributionCenters = \App\Models\DistributionCenter::all();
        return view('admin.users.partials.edit-employee', compact('employee', 'vendors', 'distributionCenters'));
    }

    public function update(Request $request, $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:Production Worker,Warehouse Staff,Driver,Sales Manager',
            'vendor_id' => 'nullable|exists:vendors,id',
            'distribution_center_id' => 'nullable|exists:distribution_centers,id',
            'status' => 'required|in:Active,On Leave,Terminated',
        ]);
        $employee->update($request->only(['name', 'role', 'vendor_id', 'distribution_center_id', 'status']));
        // Optionally update the linked user name
        if ($employee->user) {
            $employee->user->name = $request->name;
            $employee->user->save();
        }
        return redirect()->route('admin.users.index')->with('success', 'Employee updated successfully!');
    }

    public function destroy($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        // Optionally delete the linked user account
        if ($employee->user) {
            $employee->user->delete();
        }
        $employee->delete();
        return redirect()->route('admin.users.index')->with('success', 'Employee deleted successfully!');
    }
}
