<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted</title>
    <link rel="stylesheet" href="{{ asset('css/vendor-confirmation.css') }}">
</head>
<body>
<div class="confirmation-container">
    <div class="confirmation-card">
        <h2>Application Submitted!</h2>
        <p>Your vendor application has been submitted successfully.</p>
        <a href="{{ route('vendor-applicant.status', ['email' => $email]) }}" class="btn btn-success">Check Status</a>
    </div>
</div>
</body>
</html> 