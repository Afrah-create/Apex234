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
        
        return view('employee.dashboard', compact('employee'));
    }
}
