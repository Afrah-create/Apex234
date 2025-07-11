<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminEmployeeController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        $employees = Employee::with('vendor')->get();
        $vendors = Vendor::all();
        return view('admin.users.index', compact('users', 'employees', 'vendors'));
    }

    public function assignVendor(Request $request, Employee $employee)
    {
        $request->validate(['vendor_id' => 'nullable|exists:vendors,id']);
        $employee->vendor_id = $request->vendor_id;
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

        return back()->with('success', 'Vendor assignment updated!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Production Worker,Warehouse Staff,Driver,Sales Manager',
            'vendor_id' => 'nullable|exists:vendors,id',
            'status' => 'required|in:Active,On Leave,Terminated',
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
        \App\Models\Employee::create([
            'name' => $request->name,
            'role' => $request->role,
            'vendor_id' => $request->vendor_id,
            'status' => $request->status,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Employee created!');
    }

    public function edit($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $vendors = \App\Models\Vendor::all();
        return view('admin.users.partials.edit-employee', compact('employee', 'vendors'));
    }

    public function update(Request $request, $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:Production Worker,Warehouse Staff,Driver,Sales Manager',
            'vendor_id' => 'nullable|exists:vendors,id',
            'status' => 'required|in:Active,On Leave,Terminated',
        ]);
        $employee->update($request->only(['name', 'role', 'vendor_id', 'status']));
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
