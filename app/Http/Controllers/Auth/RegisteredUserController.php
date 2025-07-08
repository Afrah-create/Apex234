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
            'role' => $request->role, // Set the role column to the selected role
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

        // If registering as retailer, create retailer record
        if ($request->role === 'retailer') {
            \App\Models\Retailer::create([
                'user_id' => $user->id,
                'store_name' => $request->business_name ?? $user->name . ' Store',
                'store_code' => uniqid('STORE'),
                'store_address' => $request->business_address ?? '',
                'store_phone' => $request->phone_number ?? '',
                'store_email' => $user->email,
                'store_manager' => $user->name,
                'manager_phone' => $request->phone_number ?? '',
                'manager_email' => $user->email,
                'store_type' => 'convenience_store',
                'store_size' => 'medium',
                'daily_customer_traffic' => null,
                'monthly_sales_volume' => null,
                'payment_methods' => null,
                'store_hours' => null,
                'certification_status' => 'pending',
                'certifications' => null,
                'last_inspection_date' => null,
                'next_inspection_date' => null,
                'customer_rating' => 0,
                'status' => 'active',
                'notes' => null,
                'business_name' => $request->business_name ?? $user->name . ' Store',
                'business_address' => $request->business_address ?? '',
                'contact_person' => $user->name,
                'contact_email' => $user->email,
                'contact_phone' => $request->phone_number ?? '',
            ]);
        }

        // Note: Vendor records are created only after admin approval
        // For vendors, we only create the user and redirect to application form

        event(new Registered($user));

        Auth::login($user);

        // Redirect to role-specific dashboard
        switch ($request->role) {
            case 'retailer':
                return redirect()->route('dashboard.retailer');
            case 'supplier':
                return redirect()->route('dashboard.supplier');
            case 'vendor':
                // Store registration data in session for pre-filling application form
                $request->session()->put('vendor_registration_data', [
                    'name' => $request->name,
                    'email' => $request->email,
                    'business_name' => $request->business_name ?? '',
                    'business_address' => $request->business_address ?? '',
                    'phone_number' => $request->phone_number ?? '',
                    'tax_id' => $request->tax_id ?? '',
                    'business_license' => $request->business_license ?? '',
                    'description' => $request->description ?? '',
                ]);
                return redirect()->route('vendor-applicant.create');
            default:
                return redirect(route('dashboard', absolute: false));
        }
    }
}
