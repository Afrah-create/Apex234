<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorApplicant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class VendorApplicantController extends Controller
{
    // Show the vendor application form
    public function create()
    {
        // Get registration data from session if available
        $registrationData = session('vendor_registration_data', []);
        $name = $registrationData['name'] ?? request()->query('name');
        $email = $registrationData['email'] ?? request()->query('email');
        // If not present, fallback to logged-in vendor (if any)
        if (!$name || !$email) {
        if (Auth::check()) {
            $user = Auth::user();
            $role = null;
                if (property_exists($user, 'role')) {
                $role = $user->role;
            }
            if ($role === 'vendor') {
                $name = $user->name;
                $email = $user->email;
                }
            }
        }
        return view('vendor.apply', compact('registrationData', 'name', 'email'));
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
        $pdfContent = view('vendor.pdf', $validated)->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($pdfContent);
        $pdf->save($pdfFilePath);
        $pdfPath = 'vendor_applications/' . $pdfFileName;

        // Send PDF to Java server for validation
        $javaServerUrl = 'http://localhost:8080/api/vendors/apply'; // Update if needed
        $status = 'pending';
        $visitDate = null;
        $validationMessage = null;

        try {
            $response = \Illuminate\Support\Facades\Http::attach('file', fopen($pdfFilePath, 'r'), basename($pdfFilePath))
                ->post($javaServerUrl);

            if ($response->status() === 202) {
                // Validation passed
                $body = $response->body();
                if (preg_match('/(\\d{4}-\\d{2}-\\d{2})/', $body, $matches)) {
                    $visitDate = $matches[1];
                }
                $status = 'validated';
                $validationMessage = $body;
            } else {
                // Validation failed
                $status = 'rejected';
                $validationMessage = $response->body();
            }
        } catch (\Exception $e) {
            $status = 'error';
            $validationMessage = 'Error communicating with validation server: ' . $e->getMessage();
        }

        // Now create the vendor applicant record (after validation)
        $applicant = VendorApplicant::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company_name' => $validated['company_name'],
            'pdf_path' => $pdfPath,
            'status' => $status,
            'visit_date' => $visitDate,
            'validation_message' => $validationMessage,
        ]);

        // Clear registration data from session
        session()->forget('vendor_registration_data');

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