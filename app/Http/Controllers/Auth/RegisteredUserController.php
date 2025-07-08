<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:retailer,supplier,vendor'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign selected role
        $role = \App\Models\Role::where('name', $request->role)->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

        // If registering as supplier, create supplier record
        if ($request->role === 'supplier') {
            $supplier = \App\Models\Supplier::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name ?? 'Default Company',
                'registration_number' => uniqid('SUP'),
                'business_address' => $request->business_address ?? '',
                'contact_person' => $user->name,
                'contact_phone' => $request->contact_phone ?? '',
                'contact_email' => $user->email,
                'supplier_type' => 'dairy_farm',
                'status' => 'pending',
                'rating' => 0,
                'certifications' => null,
                'verification_date' => null,
                'contract_start_date' => null,
                'contract_end_date' => null,
                'credit_limit' => 0,
                'payment_terms_days' => 30,
                'notes' => null,
            ]);

            // Create a default dairy farm for the supplier
            \App\Models\DairyFarm::create([
                'supplier_id' => $supplier->id,
                'farm_name' => $supplier->company_name . ' Main Farm',
                'farm_code' => uniqid('FARM'),
                'farm_address' => $supplier->business_address,
                'farm_phone' => $supplier->contact_phone,
                'farm_email' => $supplier->contact_email,
                'farm_manager' => $supplier->contact_person,
                'manager_phone' => $supplier->contact_phone,
                'manager_email' => $supplier->contact_email,
                'total_cattle' => 0,
                'milking_cattle' => 0,
                'daily_milk_production' => 0,
                'certification_status' => 'pending',
                'certifications' => null,
                'last_inspection_date' => null,
                'next_inspection_date' => null,
                'quality_rating' => 0,
                'status' => 'active',
                'notes' => null,
            ]);
        }

        event(new Registered($user));

        if ($request->role === 'vendor') {
            // Do not log in vendor, redirect to application form with name/email as query params
            return redirect()->route('vendor-applicant.create', [
                'name' => $request->name,
                'email' => $request->email
            ])->with('success', 'Registration successful! Please complete your application and wait for admin approval. You will receive an email when your account is ready.');
        } else {
            Auth::login($user);
            // Redirect to role-specific dashboard
            switch ($request->role) {
                case 'retailer':
                    return redirect()->route('dashboard.retailer');
                case 'supplier':
                    return redirect()->route('dashboard.supplier');
                default:
                    return redirect(route('dashboard', absolute: false));
            }
        }
    }
}
