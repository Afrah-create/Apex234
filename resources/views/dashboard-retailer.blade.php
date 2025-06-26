@extends('layouts.app')

@section('content')
    <main class="main-content">
        <h1>Retailer</h1>
        <p>Welcome, Retailer! Use the sidebar to manage your stock, sales, and orders.</p>
        <div class="grid grid-cols-3 gap-6">
            <div class="product-card">
                <img src="/images/carousel/fresh-milk.jpg" alt="Yogurt 1">
                <div class="font-bold text-lg mb-2">Strawberry Yogurt</div>
                <div class="text-gray-500 mb-2">200ml</div>
                <div class="mt-2 text-blue-600 font-bold">₦500</div>
            </div>
            <div class="product-card">
                <img src="/images/carousel/production.jpg" alt="Yogurt 2">
                <div class="font-bold text-lg mb-2">Vanilla Yogurt</div>
                <div class="text-gray-500 mb-2">200ml</div>
                <div class="mt-2 text-blue-600 font-bold">₦500</div>
            </div>
            <div class="product-card">
                <img src="/images/carousel/quality.JPG" alt="Yogurt 3">
                <div class="font-bold text-lg mb-2">Plain Yogurt</div>
                <div class="text-gray-500 mb-2">200ml</div>
                <div class="mt-2 text-blue-600 font-bold">₦450</div>
            </div>
        </div>
    </main>
@endsection 