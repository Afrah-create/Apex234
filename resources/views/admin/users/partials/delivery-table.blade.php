<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Delivery #</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Order #</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Customer/Retailer</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Driver</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Scheduled Date</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Order Items</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @foreach($deliveries as $delivery)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $delivery->delivery_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $delivery->order->order_number ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    @if($delivery->order && $delivery->order->customer)
                        {{ $delivery->order->customer->name }}
                    @elseif($delivery->retailer)
                        {{ $delivery->retailer->store_name }}
                    @else
                        N/A
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $delivery->driver_name ?? 'Unassigned' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($delivery->delivery_status) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $delivery->scheduled_delivery_date }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    @if($delivery->order && $delivery->order->orderItems)
                        <ul>
                        @foreach($delivery->order->orderItems as $item)
                            <li>{{ $item->yogurtProduct->product_name ?? 'Product' }} - {{ $item->quantity }} x {{ $item->unit_price }} UGX</li>
                        @endforeach
                        </ul>
                    @else
                        N/A
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.deliveries.show', $delivery->id) }}" class="text-blue-600 hover:underline">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Drivers Delivery Loads</h2>
    <div class="relative" style="height: 350px;">
        <canvas id="driverDeliveryLoadChart"></canvas>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/api/admin/driver-delivery-loads')
            .then(res => res.json())
            .then(data => {
                const ctx = document.getElementById('driverDeliveryLoadChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Deliveries',
                            data: data.data,
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Number of Deliveries per Driver' }
                        },
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Deliveries' } },
                            x: { title: { display: true, text: 'Driver' } }
                        }
                    }
                });
            });
    });
</script> 