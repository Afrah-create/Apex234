<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Vendor;
use Illuminate\Http\Request;

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
                $user->notify(new \App\Notifications\EmployeeAssignedToVendor($employee->role, $vendor));
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
}
