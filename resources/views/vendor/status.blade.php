<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Vendor Application Status</title>
    <style>
body {
    background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
    margin: 0;
    color: #22223b;
}
.status-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
}
.status-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(44, 62, 80, 0.10);
    padding: 2.5rem 2rem;
    max-width: 480px;
    width: 100%;
    transition: box-shadow 0.2s;
    text-align: center;
}
.status-card h2 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 1.2rem;
    letter-spacing: 1px;
}
.status-info {
    margin: 1.5rem 0;
    font-size: 1.1rem;
    color: #34495e;
}
.status-badge {
    display: inline-block;
    padding: 0.5rem 1.2rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 1.2rem;
}
.status-badge.pending {
    background: #fef9c3;
    color: #b45309;
    border: 1.5px solid #fde68a;
}
.status-badge.validated {
    background: #d1fae5;
    color: #047857;
    border: 1.5px solid #6ee7b7;
}
.status-badge.rejected {
    background: #fee2e2;
    color: #b91c1c;
    border: 1.5px solid #fca5a5;
}
.status-badge.error {
    background: #f3f4f6;
    color: #374151;
    border: 1.5px solid #d1d5db;
}
.status-details {
    background: #f8fafc;
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1.2rem;
    font-size: 1rem;
    color: #475569;
    text-align: left;
}
.status-actions {
    margin-top: 2rem;
}
.status-actions .btn {
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    border: none;
    border-radius: 14px;
    padding: 0.85rem 2.2rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.08);
    transition: background 0.2s, box-shadow 0.2s;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}
.status-actions .btn:hover {
    background: linear-gradient(90deg, #0056b3 0%, #007bff 100%);
    box-shadow: 0 4px 16px rgba(0,123,255,0.13);
}
@media (max-width: 600px) {
    .status-card {
        padding: 1.2rem 0.5rem;
    }
    .status-details {
        font-size: 0.98rem;
    }
}
    </style>
</head>
<body>
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
                <span class="status-badge {{ $applicant->status === 'approved' ? 'validated' : 'pending' }}">
                    Admin Approval: {{ $applicant->status === 'approved' ? 'Approved' : 'Pending' }}
                </span>
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
            <div class="status-actions" style="margin-top: 1.5rem;">
                <a href="/" class="btn">Exit</a>
            </div>
        @endif
    </div>
</div>
</body>
</html> 