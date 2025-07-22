<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $scheduledReport->name ?? 'Report' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #222; background: #f9f9f9; }
        .container { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; max-width: 600px; margin: 30px auto; padding: 32px; }
        .header { text-align: center; margin-bottom: 24px; }
        .logo { max-width: 120px; margin-bottom: 8px; }
        .company { font-size: 1.3em; font-weight: bold; color: #2563eb; margin-bottom: 2px; }
        .report-title { font-size: 1.1em; font-weight: bold; margin-bottom: 8px; }
        .section-title { font-size: 1em; font-weight: bold; margin-top: 24px; margin-bottom: 8px; color: #444; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; }
        th { background: #f3f4f6; color: #222; }
        .summary-table th { width: 40%; }
        .footer { margin-top: 32px; text-align: center; color: #888; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/apex-logo.png') }}" alt="Company Logo" class="logo">
            <div class="company">Apex Dairy (Caramel YG)</div>
            <div class="report-title">{{ $scheduledReport->name ?? 'Report' }}</div>
            <div style="color:#888; font-size:13px;">Generated at: {{ now()->format('Y-m-d H:i:s') }}</div>
        </div>
        <div>
            <div style="margin-bottom:18px;">
                <p>Dear Team,</p>
                <p>Please find attached your scheduled report from Apex Dairy (Caramel YG). Below is a summary and details of the report. If you have any questions or need further analysis, feel free to reply to this email.</p>
            </div>
            <div class="section-title">Description</div>
            <div style="margin-bottom:12px;">{{ $scheduledReport->description }}</div>
            <div class="section-title">Summary</div>
            @if(isset($reportData['summary']) && is_array($reportData['summary']))
                <table class="summary-table">
                    <tbody>
                        @foreach($reportData['summary'] as $key => $value)
                            <tr>
                                <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                <td>
                                    @if(is_array($value))
                                        {{ json_encode($value) }}
                                    @else
                                        {{ is_null($value) ? 'N/A' : $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="color:#888;">No summary available.</div>
            @endif
            <div class="section-title">Details</div>
            @if(isset($reportData['data']) && is_array($reportData['data']) && count($reportData['data']))
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                @foreach(array_keys((array)($reportData['data'][0] ?? [])) as $col)
                                    <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData['data'] as $row)
                                <tr>
                                    @foreach($row as $cell)
                                        <td>
                                            @if(is_array($cell))
                                                {{ json_encode($cell) }}
                                            @else
                                                {{ is_null($cell) ? 'N/A' : $cell }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="color:#888;">No details available for this report.</div>
            @endif
        </div>
        <div class="footer">
            <p style="margin-bottom:8px;">Thank you for your continued partnership with Apex Dairy.<br>For support or inquiries, contact us at <a href="mailto:support@apexdairy.com">support@apexdairy.com</a>.</p>
            &copy; {{ date('Y') }} Apex Dairy (Caramel YG). All rights reserved.
        </div>
    </div>
</body>
</html> 