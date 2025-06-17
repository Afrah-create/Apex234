<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 

class DashboardController extends Controller
{
    public function index()
    {
        // You can fetch more data here for the dashboard

        return view('dashboard');
    }
}
