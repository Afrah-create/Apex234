<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class AdminWorkforceController extends Controller
{
    public function getWorkforceDistribution(): JsonResponse
    {
        $roles = ['Production Worker', 'Warehouse Staff', 'Driver', 'Sales Manager'];
        $counts = Employee::select('role', DB::raw('count(*) as total'))
            ->whereIn('role', $roles)
            ->groupBy('role')
            ->get();

        $chartData = [
            'labels' => $counts->pluck('role'),
            'datasets' => [
                [
                    'data' => $counts->pluck('total'),
                    'backgroundColor' => ['#6366f1', '#10b981', '#f59e42', '#ef4444'],
                ]
            ]
        ];

        return response()->json($chartData);
    }
}
