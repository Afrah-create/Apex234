<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Assignments Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #888; padding: 4px 6px; text-align: left; }
        th { background: #f3f3f3; }
        h2 { margin-bottom: 0; }
        .small { font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h2>Employee Assignments Report</h2>
    <div class="small">Generated: {{ date('Y-m-d H:i') }}</div>
    <table>
        <thead>
            <tr>
                <th>Date Assigned</th>
                <th>Vendor</th>
                <th>Employee Name</th>
                <th>Employee Email</th>
                <th>Role/Position</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->created_at ? $employee->created_at->format('Y-m-d') : '' }}</td>
                <td>{{ $employee->vendor ? $employee->vendor->name : '-' }}</td>
                <td>{{ $employee->user ? $employee->user->name : '-' }}</td>
                <td>{{ $employee->user ? $employee->user->email : '-' }}</td>
                <td>{{ $employee->role ?? '-' }}</td>
                <td>{{ $employee->status ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html> 