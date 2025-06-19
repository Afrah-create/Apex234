@extends('layouts.app', ['activePage' => 'supplier_dashboard', 'title' => 'Supplier Dashboard', 'navName' => 'Supplier Dashboard', 'activeButton' => 'laravel'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Supplier Dashboard</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                <img src="{{ asset('images/profile-user (2).png')}}" alt="Supplier" class="img-fluid mb-3" style="height:100px;width:100px;">
                                    <h5>Supplier Profile</h5>
                                    <p>Manage your profile and contact information.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/orders.png')}}" alt="Orders" class="img-fluid mb-3" style="height:100px;width:100px;">
                                    <h5>Orders</h5>
                                    <p>View and manage supply orders.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/inventory.png')}}" alt="Inventory" class="img-fluid mb-3" style="height:100px;width:100px;">
                                    <h5>Inventory</h5>
                                    <p>Track your raw materials and product inventory.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/reports.png')}}" alt="Reports" class="img-fluid mb-3" style="height:100px;width:100px;">
                                    <h5>Reports</h5>
                                    <p>View supply chain and delivery reports.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/customer-service.png')}}" alt="Support" class="img-fluid mb-3" style="height:100px;width:100px;">
                                    <h5>Support</h5>
                                    <p>Contact support for assistance.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 