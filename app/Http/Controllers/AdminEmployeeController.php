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
        $employees = Employee::with(['vendor', 'distributionCenter'])->get();
        $vendors = Vendor::where('status', 'approved')->get();
        $distributionCenters = \App\Models\DistributionCenter::orderBy('center_name')->paginate(15);
        $vendorApplicants = \App\Models\VendorApplicant::whereIn('status', ['validated', 'pending'])->get();
        return view('admin.users.index', compact('users', 'employees', 'vendors', 'distributionCenters', 'vendorApplicants'));
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
                    Log::error('Failed to send notification: ' . $e->getMessage());
                    return back()->with('warning', 'Vendor assignment updated, but notification failed to send.');
                }
                // --- Send chat message to employee ---
                $admin = auth()->user();
                $distCenter = $employee->distribution_center_id ? \App\Models\DistributionCenter::find($employee->distribution_center_id) : null;
                $chatMsg = "Hello {$user->name},\n" .
                    "You have been assigned a new task by Admin: {$admin->name}.\n" .
                    "Role/Position: {$employee->role}\n" .
                    ($vendor ? "Vendor: {$vendor->name}\n" : "") .
                    ($distCenter ? "Distribution Center: {$distCenter->center_name}\n" : "") .
                    (isset($employee->status) ? "Status: {$employee->status}\n" : "") .
                    (request('deadline') ? "Deadline: " . request('deadline') . "\n" : "") .
                    "Thank you for your dedication!";
                \App\Models\ChatMessage::create([
                    'sender_id' => $admin->id,
                    'receiver_id' => $user->id,
                    'message' => $chatMsg,
                    'is_read' => false
                ]);
                // --- End chat message ---
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
            'role' => 'required|in:Production Worker,Warehouse Staff,Driver,Sales Manager',
            'vendor_id' => 'nullable|exists:vendors,id',
            'distribution_center_id' => 'nullable|exists:distribution_centers,id',
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
            'distribution_center_id' => $request->distribution_center_id,
            'status' => $request->status,
            'user_id' => $user->id,
        ]);

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

    // Export filtered employee assignments as CSV
    public function exportCsv(Request $request)
    {
        $query = \App\Models\Employee::with(['user', 'vendor']);
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->input('vendor_id'));
        }
        $employees = $query->orderByDesc('created_at')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employee_assignments.csv"',
        ];
        $columns = [
            'Date Assigned', 'Vendor', 'Employee Name', 'Employee Email', 'Role/Position', 'Status'
        ];
        $callback = function() use ($employees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->created_at ? $employee->created_at->format('Y-m-d') : '',
                    $employee->vendor ? $employee->vendor->name : '-',
                    $employee->user ? $employee->user->name : '-',
                    $employee->user ? $employee->user->email : '-',
                    $employee->role ?? '-',
                    $employee->status ?? '-',
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    // Export filtered employee assignments as PDF
    public function exportPdf(Request $request)
    {
        $query = \App\Models\Employee::with(['user', 'vendor']);
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->input('vendor_id'));
        }
        $employees = $query->orderByDesc('created_at')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.employee_assignments_pdf', [
            'employees' => $employees
        ])->setPaper('a4', 'landscape');
        return $pdf->download('employee_assignments.pdf');
    }
}
