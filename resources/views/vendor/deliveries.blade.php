@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">My Deliveries</h2>
    <table class="min-w-full bg-white">
        <thead>
            <tr>
                <th>Delivery #</th>
                <th>Status</th>
                <th>Scheduled Date</th>
                <th>Recipient</th>
                <th>Address</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deliveries as $delivery)
            <tr>
                <td>{{ $delivery->delivery_number }}</td>
                <td>{{ ucfirst($delivery->delivery_status) }}</td>
                <td>{{ $delivery->scheduled_delivery_date }} {{ $delivery->scheduled_delivery_time }}</td>
                <td>{{ $delivery->recipient_name }}</td>
                <td>{{ $delivery->delivery_address }}</td>
                <td>
                    @if($delivery->pdf_path)
                        <a href="{{ asset('storage/' . $delivery->pdf_path) }}" target="_blank" class="text-blue-600 underline">View PDF</a>
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 