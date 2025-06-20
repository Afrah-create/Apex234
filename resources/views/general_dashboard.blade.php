@extends('layouts.app', ['activePage' => 'general_dashboard', 'title' => 'General Dashboard', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@section('content')
<div class="container-fluid" style="background: linear-gradient(135deg, #f8fafc 0%, #e3e6ed 100%); min-height: 100vh; padding-top: 60px; padding-bottom: 60px;">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="text-center p-5 mb-4" style="background: #fff; border-radius: 1.5rem; box-shadow: 0 4px 24px rgba(44,62,80,0.08);">
                <img src="{{ asset('light-bootstrap/img/apex-logo.png') }}" alt="Logo" class="mb-4" style="height: 80px; width: 80px; border-radius: 50%; background: #e3e6ed;">
                <h1 class="display-5 fw-bold mb-3" style="color: #2c3e50;">Caramel Yoghurt Supply Chain</h1>
                <p class="lead mb-4" style="color: #6c757d; font-size: 1.25rem;">Welcome to the unified dashboard. Choose your role to continue to your personalized experience.</p>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0" style="border-radius: 1.5rem;">
                <div class="card-body py-5 px-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="d-flex flex-column align-items-center">
                                <img src="{{ asset('/light-bootstrap/img/supplier.png')}}" alt="Supplier" class="mb-3" style="height: 70px; width: 70px; border-radius: 50%; background: #f8fafc;">
                                <h4 class="mb-2" style="color: #34495e;">Supplier Dashboard</h4>
                                <a href="{{ route('supplier.dashboard') }}" class="btn btn-primary btn-lg px-5 py-2 mt-2 shadow-sm" style="border-radius: 2rem; font-weight: 500; letter-spacing: 1px;">Go to Supplier</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column align-items-center">
                                <img src="{{ asset('/light-bootstrap/img/customer.png')}}" alt="Customer" class="mb-3" style="height: 70px; width: 70px; border-radius: 50%; background: #f8fafc;">
                                <h4 class="mb-2" style="color: #34495e;">Customer Dashboard</h4>
                                <a href="{{ route('customer.dashboard') }}" class="btn btn-success btn-lg px-5 py-2 mt-2 shadow-sm" style="border-radius: 2rem; font-weight: 500; letter-spacing: 1px;">Go to Customer</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 