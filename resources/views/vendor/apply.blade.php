<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Application Form</title>
    <link rel="stylesheet" href="{{ asset('css/vendor-application.css') }}">
</head>
<body>
<div class="vendor-form-container">
    <div class="vendor-form-card">
        <h2>Vendor Application Form</h2>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('vendor-applicant.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $name ?? ($registrationData['name'] ?? '')) }}" required @if(isset($name) && $name) readonly @endif>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $email ?? ($registrationData['email'] ?? '')) }}" required @if(isset($email) && $email) readonly @endif>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $registrationData['phone_number'] ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $registrationData['business_name'] ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label for="annual_revenue" class="form-label">Annual Revenue (USD)</label>
                <input type="number" class="form-control" id="annual_revenue" name="annual_revenue" value="{{ old('annual_revenue') }}" required min="0">
            </div>
            <div class="mb-3">
                <label for="reference" class="form-label">Reference</label>
                <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference') }}" required>
            </div>
            <div class="mb-3">
                <label for="license_number" class="form-label">License Number</label>
                <input type="text" class="form-control" id="license_number" name="license_number" value="{{ old('license_number', $registrationData['business_license'] ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label for="compliance_certificate" class="form-label">Compliance Certificate</label>
                <select class="form-control" id="compliance_certificate" name="compliance_certificate" required>
                    <option value="">Select</option>
                    <option value="Yes" {{ old('compliance_certificate') == 'Yes' ? 'selected' : '' }}>Yes</option>
                    <option value="No" {{ old('compliance_certificate') == 'No' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit Application</button>
        </form>
    </div>
</div>
</body>
</html> 