<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // example model

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count(); // Example data
        // You can fetch more data here for the dashboard

        return view('dashboard', compact('userCount'));
    }
}
