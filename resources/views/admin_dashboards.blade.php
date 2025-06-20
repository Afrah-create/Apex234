@extends('layouts.app')

@php($activePage = 'admin')
@php($activeButton = '')

@section('content')
<div class="wrapper">
    <div class="d-flex">
        <!-- Sidebar -->
        <div>
            @include('layouts.navbars.sidebar')
        </div>
        <!-- Main Content -->
        <div class="main-panel flex-grow-1">
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="card-title">Admin Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <p>Welcome, Admin! Here you can manage users, view reports, and perform administrative tasks.</p>
                        <!-- Add more admin widgets or content here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 