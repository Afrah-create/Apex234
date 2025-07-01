<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vendor Application PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #ddd; padding: 8px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Vendor Application</h1>
    <p>Annual Revenue: ${{ number_format($annual_revenue, 0, '.', ',') }}</p>
    <p>Reference: {{ $reference }}</p>
    <p>License Number: {{ $license_number }}</p>
    <p>Compliance Certificate: {{ $compliance_certificate }}</p>
    <table>
        <tr><th>Name</th><td>{{ $name }}</td></tr>
        <tr><th>Email</th><td>{{ $email }}</td></tr>
        <tr><th>Phone</th><td>{{ $phone }}</td></tr>
        <tr><th>Company Name</th><td>{{ $company_name }}</td></tr>
    </table>
</body>
</html> 