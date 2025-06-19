@extends('layouts.app', ['activePage' => 'general_dashboard', 'title' => 'General Dashboard', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="card-title">Welcome to the Caramel Yoghurt Supply Chain Management System</h4>
                </div>
                <div class="card-body text-center">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <a href="{{ route('supplier.dashboard') }}" class="btn btn-primary btn-lg w-100">
                                Supplier Dashboard
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-secondary btn-lg w-100 disabled">
                                Other Dashboard (Coming Soon)
                            </a>
                        </div>
                    </div>
                    <img src="" alt="General Dashboard" class="img-fluid" style="height:200px;width:200px;">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 