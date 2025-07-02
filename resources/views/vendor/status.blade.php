@extends('layouts.app')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('css/vendor-status.css') }}">
</head>
<div class="status-container">
    <div class="status-card">
        <h2>Check Vendor Application Status</h2>
        <form method="GET" action="{{ route('vendor-applicant.status') }}" class="mb-4">
            <div class="mb-3">
                <label for="email" class="form-label">Enter your email address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $email) }}" required>
            </div>
            <div class="status-actions">
                <button type="submit" class="btn">Check Status</button>
            </div>
        </form>

        @if($email && !$applicant)
            <div class="status-info status-badge error">No application found for this email.</div>
        @endif

        @if($applicant)
            <div class="status-info">
                <span class="status-badge {{ $applicant->status }}">{{ ucfirst($applicant->status) }}</span>
            </div>
            <div class="status-details">
                <p><strong>Name:</strong> {{ $applicant->name }}</p>
                <p><strong>Company:</strong> {{ $applicant->company_name }}</p>
                <p><strong>Email:</strong> {{ $applicant->email }}</p>
                <p><strong>Phone:</strong> {{ $applicant->phone }}</p>
                @if($applicant->visit_date)
                    <p><strong>Scheduled Visit Date:</strong> {{ $applicant->visit_date }}</p>
                @endif
                @if($applicant->validation_message)
                    <p><strong>Validation Message:</strong><br>{{ $applicant->validation_message }}</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection 