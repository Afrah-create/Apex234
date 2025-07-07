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
        // Only create user and approve when admin clicks Confirm
        $user = User::where('email', $applicant->email)->first();
        $password = Str::random(10);
        if (!$user) {
            $user = User::create([
                'name' => $applicant->name,
                'email' => $applicant->email,
                'password' => Hash::make($password),
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
        $applicant->update(['status' => 'approved']);
        // Send email to vendor
        Mail::to($applicant->email)->send(new \App\Mail\VendorApprovedMail($user, $password));
        return redirect()->back()->with('success', 'Vendor approved and notified by email.');
    }
} 