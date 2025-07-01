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
        return back()->with('success', 'Vendor assignment updated!');
    }
}
