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

    // New: Assign role to employee (and create Driver record if needed)
    public function assignRole(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $request->validate([
            'role' => 'required|in:Warehouse Staff,Driver',
        ]);
        $oldRole = $employee->role;
        $employee->role = $request->role;
        $employee->save();
        // If assigning as Driver, create Driver record if not exists
        if ($request->role === 'Driver') {
            $driver = \App\Models\Driver::where('employee_id', $employee->id)->first();
            if (!$driver) {
                \App\Models\Driver::create([
                    'employee_id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->user ? $employee->user->email : null,
                    'phone' => $employee->user ? $employee->user->mobile ?? $employee->user->phone ?? null : null,
                    'status' => 'active',
                ]);
            }
        }
        // Optionally: If switching from Driver to Warehouse Staff, you may want to deactivate the driver record
        if ($oldRole === 'Driver' && $request->role === 'Warehouse Staff') {
            $driver = \App\Models\Driver::where('employee_id', $employee->id)->first();
            if ($driver) {
                $driver->status = 'inactive';
                $driver->save();
            }
        }
        return back()->with('success', 'Role assigned successfully.');
    }

    // New: Assign vendor to employee
    public function assignVendor(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);
        $employee->vendor_id = $request->vendor_id;
        $employee->save();
        return back()->with('success', 'Vendor assigned successfully.');
    }

    // New: Assign distribution center to employee
    public function assignDistributionCenter(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $request->validate([
            'distribution_center_id' => 'nullable|exists:distribution_centers,id',
        ]);
        $employee->distribution_center_id = $request->distribution_center_id;
        $employee->save();
        return back()->with('success', 'Distribution center assigned successfully.');
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
