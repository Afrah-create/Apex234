@extends('layouts.app', ['activePage' => 'customer_dashboard', 'title' => 'Shop Caramel Yoghurt', 'navName' => 'Customer Shopping', 'activeButton' => 'laravel'])

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header text-center">
                    <h4 class="card-title">Shop Caramel Yoghurt Flavours</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/vanilla.jpeg') }}" alt="Vanilla Flavour" class="img-fluid mb-3" style="height:120px;width:120px;">
                                    <h5>Vanilla Caramel Yoghurt</h5>
                                    <p>Creamy caramel yoghurt with a hint of vanilla.</p>
                                    <div class="mb-2">
                                        <input type="number" class="form-control" value="1" min="1" style="width:80px;display:inline-block;">
                                    </div>
                                    <button class="btn btn-primary w-100">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/strawberry.jpeg') }}" alt="Strawberry Flavour" class="img-fluid mb-3" style="height:120px;width:120px;">
                                    <h5>Strawberry Caramel Yoghurt</h5>
                                    <p>Sweet caramel yoghurt blended with strawberries.</p>
                                    <div class="mb-2">
                                        <input type="number" class="form-control" value="1" min="1" style="width:80px;display:inline-block;">
                                    </div>
                                    <button class="btn btn-primary w-100">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/mango.jpeg') }}" alt="Mango Flavour" class="img-fluid mb-3" style="height:120px;width:120px;">
                                    <h5>Mango Caramel Yoghurt</h5>
                                    <p>Exotic caramel yoghurt with mango flavour.</p>
                                    <div class="mb-2">
                                        <input type="number" class="form-control" value="1" min="1" style="width:80px;display:inline-block;">
                                    </div>
                                    <button class="btn btn-primary w-100">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header text-center">
                    <h5 class="card-title">My Cart</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Flavour</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Vanilla Caramel Yoghurt</td>
                                    <td>2</td>
                                    <td>$2.00</td>
                                    <td>$4.00</td>
                                    <td><button class="btn btn-danger btn-sm">Remove</button></td>
                                </tr>
                                <tr>
                                    <td>Strawberry Caramel Yoghurt</td>
                                    <td>1</td>
                                    <td>$2.50</td>
                                    <td>$2.50</td>
                                    <td><button class="btn btn-danger btn-sm">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        <h5 class="mr-3">Total: $6.50</h5>
                        <button class="btn btn-success">Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 