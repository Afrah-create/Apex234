<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Analysis Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
        h1, h2 { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>User Analysis Report</h1>
    <p><strong>Report Name:</strong> {{ $report_name }}</p>
    <p><strong>Generated At:</strong> {{ $generated_at }}</p>

    <h2>Summary</h2>
    <table>
        <tbody>
            @foreach($summary as $key => $value)
                <tr>
                    <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>User Details</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Key Metrics</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $user)
                <tr>
                    <td>{{ $user['name'] ?? '' }}</td>
                    <td>{{ $user['email'] ?? '' }}</td>
                    <td>
                        @if(isset($user['roles']) && is_array($user['roles']) && count($user['roles']))
                            {{ $user['roles'][0]['name'] ?? '' }}
                        @else
                            {{ $user['role'] ?? '' }}
                        @endif
                    </td>
                    <td>{{ $user['status'] ?? '' }}</td>
                    <td>
                        @if(isset($user['role_metrics']) && is_array($user['role_metrics']))
                            @foreach($user['role_metrics'] as $k => $v)
                                <div><strong>{{ ucwords(str_replace('_', ' ', $k)) }}:</strong> {{ is_null($v) ? 'N/A' : $v }}</div>
                            @endforeach
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 