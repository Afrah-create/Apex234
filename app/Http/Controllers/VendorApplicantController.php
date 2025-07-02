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
            'annual_revenue' => 'required|numeric|min:0',
            'reference' => 'required|string|max:255',
            'license_number' => 'required|string|max:255',
            'compliance_certificate' => 'required|in:Yes,No',
        ]);

        // Generate PDF from form data
        $pdfFileName = 'vendor_application_' . time() . '.pdf';
        $pdfDir = storage_path('app/public/vendor_applications');
        if (!file_exists($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }
        $pdfFilePath = $pdfDir . '/' . $pdfFileName;
        // Use dompdf to generate PDF
        $pdfContent = view('vendor.pdf', $validated)->render();
        $pdf = \PDF::loadHTML($pdfContent);
        $pdf->save($pdfFilePath);
        $pdfPath = 'vendor_applications/' . $pdfFileName;

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
        try {
            $response = \Illuminate\Support\Facades\Http::attach('file', fopen($pdfFilePath, 'r'), basename($pdfFilePath))
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

        // Redirect to confirmation page with check status button
        return redirect()->route('vendor-applicant.confirmation', ['email' => $validated['email']]);
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

    // Show the confirmation page after application submission
    public function confirmation(Request $request)
    {
        $email = $request->query('email');
        return view('vendor.confirmation', compact('email'));
    }
} 