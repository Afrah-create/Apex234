@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Vendor Dashboard</h1>
    <div>
        <img src="" alt="Vendor Image" style="max-width:200px;">
    </div>
    <div class="mt-4">
        <h2>Vendor Stats</h2>
        <ul>
            <li>Total Orders: <!-- Add dynamic value here --></li>
            <li>Pending Shipments: <!-- Add dynamic value here --></li>
        </ul>
    </div>
</div>
@endsection 