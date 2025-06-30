<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorApplicant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class VendorApplicantController extends Controller
{
    // Show the vendor application form
    public function create()
    {
        return view('vendor.apply');
    }

    // Handle the form submission and PDF upload
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'company_name' => 'required|string|max:255',
            'pdf' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Store the PDF
        $pdfPath = $request->file('pdf')->store('vendor_applications', 'public');

        // Create the vendor applicant record (initially pending)
        $applicant = VendorApplicant::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company_name' => $validated['company_name'],
            'pdf_path' => $pdfPath,
            'status' => 'pending',
        ]);

        // Send PDF to Java server for validation
        $javaServerUrl = 'http://localhost:8080/api/vendors/apply'; // Update if needed
        $pdfFilePath = storage_path('app/public/' . $pdfPath);
        try {
            $response = Http::attach('file', fopen($pdfFilePath, 'r'), basename($pdfFilePath))
                ->post($javaServerUrl);

            if ($response->status() === 202) {
                // Validation passed, extract visit date from response
                $body = $response->body();
                $visitDate = null;
                if (preg_match('/(\\d{4}-\\d{2}-\\d{2})/', $body, $matches)) {
                    $visitDate = $matches[1];
                }
                $applicant->update([
                    'status' => 'validated',
                    'visit_date' => $visitDate,
                    'validation_message' => $body,
                ]);
            } else {
                // Validation failed
                $applicant->update([
                    'status' => 'rejected',
                    'validation_message' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            $applicant->update([
                'status' => 'error',
                'validation_message' => 'Error communicating with validation server: ' . $e->getMessage(),
            ]);
        }

        return redirect()->back()->with('success', 'Application submitted successfully!');
    }

    // Show the status of a vendor application by email
    public function status(Request $request)
    {
        $email = $request->query('email');
        $applicant = null;
        if ($email) {
            $applicant = VendorApplicant::where('email', $email)->latest()->first();
        }
        return view('vendor.status', compact('applicant', 'email'));
    }
} 