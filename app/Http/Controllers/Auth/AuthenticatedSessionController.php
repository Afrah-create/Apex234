<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use App\Models\VendorApplicant;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        
        if ($user instanceof \App\Models\User) {
            $role = $user->getPrimaryRoleName();
        } else {
            $role = null;
        }
        
        switch ($role) {
            case 'admin':
                return redirect()->route('dashboard');
            case 'retailer':
                return redirect()->route('dashboard.retailer');
            case 'supplier':
                return redirect()->route('dashboard.supplier');
            case 'vendor':
                $vendorApplicant = VendorApplicant::where('email', $user->email)->latest()->first();
                if (!$vendorApplicant || $vendorApplicant->status === 'pending') {
                    return redirect()->route('vendor-applicant.status', ['email' => $user->email]);
                }
                return redirect()->route('dashboard.vendor');
            case 'employee':
                return redirect()->route('dashboard.employee');
            default:
                // Check if user has an employee record and redirect accordingly
                $employeeRecord = \App\Models\Employee::where('user_id', $user->id)->first();
                if ($employeeRecord) {
                    return redirect()->route('dashboard.employee');
                }
                return redirect()->route('dashboard');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Set a session flash variable to indicate logout just happened
        session()->flash('force_refresh', true);

        return redirect('/');
    }
}
