<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report_name ?? 'Report' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
        h1, h2 { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>{{ $report_name ?? 'Report' }}</h1>
    <p><strong>Generated At:</strong> {{ $generated_at ?? now()->format('Y-m-d H:i:s') }}</p>

    @if(!empty($summary))
    <h2>Summary</h2>
    <table>
        <tbody>
            @foreach($summary as $key => $value)
                <tr>
                    <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                    <td>{{ is_array($value) ? json_encode($value) : (is_null($value) ? 'N/A' : $value) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(!empty($data))
    <h2>Details</h2>
    <table>
        <thead>
            <tr>
                @foreach(array_keys((array)($data[0] ?? [])) as $col)
                    <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>
                            @if(is_array($cell))
                                @foreach($cell as $k => $v)
                                    <div><strong>{{ ucwords(str_replace('_', ' ', $k)) }}:</strong> {{ is_array($v) ? json_encode($v) : (is_null($v) ? 'N/A' : $v) }}</div>
                                @endforeach
                            @else
                                {{ is_null($cell) ? 'N/A' : $cell }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No data available for this report.</p>
    @endif
</body>
</html> 