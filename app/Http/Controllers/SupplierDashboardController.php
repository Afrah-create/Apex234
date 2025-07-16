<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierDashboardController extends Controller
{
    public function reportsPage()
    {
        return view('supplier.my-reports');
    }

    public function myReports(Request $request)
    {
        $user = $request->user();
        $role = 'supplier';
        $userId = $user->id;
        $email = $user->email;

        $reports = \App\Models\ReportLog::whereHas('scheduledReport', function($q) use ($role, $userId) {
                $q->where('stakeholder_type', $role)
                  ->where('stakeholder_id', $userId);
            })
            ->orWhereJsonContains('recipients', $email)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $reports]);
    }
} 