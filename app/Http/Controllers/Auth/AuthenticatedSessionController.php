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
        $role = $user->getPrimaryRoleName();
        if ($role === 'vendor' && !$user->isApproved()) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your vendor account is pending admin approval. Please wait for admin approval before logging in.'
            ]);
        }
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
                    return redirect()->route('vendor-applicant.create');
                }
                return redirect()->route('dashboard.vendor');
            case 'employee':
                return redirect()->route('dashboard.employee');
            default:
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

        return redirect('/');
    }
}
