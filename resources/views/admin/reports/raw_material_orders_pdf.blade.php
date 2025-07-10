<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raw Material Orders Report</title>
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
    <h2>Raw Material Orders Report</h2>
    <div class="small">Generated: {{ date('Y-m-d H:i') }}</div>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Vendor</th>
                <th>Supplier</th>
                <th>Material</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Expected</th>
                <th>Actual</th>
            </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->vendor ? ($order->vendor->name ?? $order->vendor->email ?? '-') : '-' }}</td>
                <td>{{ $order->supplier && $order->supplier->user ? ($order->supplier->user->name ?? '-') : '-' }}</td>
                <td>{{ $order->material_name }} ({{ $order->material_type }})</td>
                <td>{{ $order->quantity }}</td>
                <td>{{ $order->unit_of_measure }}</td>
                <td>{{ number_format($order->unit_price, 2) }}</td>
                <td>{{ number_format($order->total_amount, 2) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>{{ $order->order_date ? $order->order_date->format('Y-m-d') : '' }}</td>
                <td>{{ $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : '' }}</td>
                <td>{{ $order->actual_delivery_date ? $order->actual_delivery_date->format('Y-m-d') : '' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html> 