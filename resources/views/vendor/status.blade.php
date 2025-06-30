@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Check Vendor Application Status</h2>
    <form method="GET" action="{{ route('vendor-applicant.status') }}" class="mb-4">
        <div class="mb-3">
            <label for="email" class="form-label">Enter your email address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $email) }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Check Status</button>
    </form>

    @if($email && !$applicant)
        <div class="alert alert-warning">No application found for this email.</div>
    @endif

    @if($applicant)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Application Status: <span class="badge bg-info">{{ ucfirst($applicant->status) }}</span></h5>
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
        </div>
    @endif
</div>
@endsection 