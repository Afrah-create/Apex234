@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Users</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $userCount }}</h5>
                </div>
            </div>
        </div>
        <!-- Add more cards for orders, revenue, etc. -->
    </div>
</div>
@endsection