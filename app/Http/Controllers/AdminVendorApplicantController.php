<?php

namespace App\Http\Controllers;

use App\Models\VendorApplicant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminVendorApplicantController extends Controller
{
    public function approve($id)
    {
        $applicant = VendorApplicant::findOrFail($id);
        
        // Check if user already exists
        $user = User::where('email', $applicant->email)->first();
        $password = Str::random(10);
        
        if (!$user) {
            $user = User::create([
                'name' => $applicant->name,
                'email' => $applicant->email,
                'password' => Hash::make($password),
                'role' => 'vendor',
                'status' => 'approved',
            ]);
        } else {
            $user->update(['status' => 'approved']);
        }
        
        // Assign vendor role
        $vendorRole = Role::where('name', 'vendor')->first();
        if ($vendorRole) {
            $user->roles()->syncWithoutDetaching([$vendorRole->id]);
        }
        
        // Create vendor record with business details
        $vendor = \App\Models\Vendor::create([
            'user_id' => $user->id,
            'business_name' => $applicant->company_name,
            'business_address' => '', // Not available in applicant data, can be updated later
            'phone_number' => $applicant->phone,
            'tax_id' => null, // Not available in applicant data, can be updated later
            'business_license' => $applicant->license_number,
            'status' => 'approved',
            'description' => 'Vendor approved from application - ID: ' . $applicant->id,
            'contact_person' => $applicant->name,
            'contact_email' => $applicant->email,
            'contact_phone' => $applicant->phone,
        ]);
        
        $applicant->update(['status' => 'approved']);
        
        // Send email to vendor
        Mail::to($applicant->email)->send(new \App\Mail\VendorApprovedMail($user, $password));
        
        return redirect()->back()->with('success', 'Vendor approved and notified by email.');
    }
} 