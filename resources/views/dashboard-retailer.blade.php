@extends('layouts.app')

@section('content')
    <main class="main-content">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-green-700">You are most welcome, {{ strtoupper(auth()->user()->name) }}</h2>
        </div>
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Products Offered</h3>
       
        <div class="flex justify-end mb-4">
            <button id="cart-toggle-btn" class="relative bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A1 1 0 007 17h10a1 1 0 00.95-.68L19 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7"></path></svg>
                <span>Cart</span>
                <span id="cart-count" class="ml-2 bg-white text-green-700 rounded-full px-2 py-0.5 text-xs font-bold">0</span>
            </button>
        </div>
        <div class="grid grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="product-card" data-product-id="{{ $product->id }}">
                    <img src="{{ $product->image_path ? asset('storage/' . $product->image_path) : '/images/carousel/fresh-milk.jpg' }}" alt="{{ $product->product_name }}">
                    <div class="font-bold text-lg mb-2">{{ $product->product_name }}</div>
                    <div class="text-gray-500 mb-2">{{ $product->package_size ?? '200ml' }}</div>
                    <div class="mt-2 text-blue-600 font-bold">UGX {{ number_format($product->selling_price, 2) }}</div>
                    <button class="add-to-cart-btn mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Add to Cart</button>
                </div>
            @endforeach
        </div>
        <!-- Cart Sidebar/Modal -->
        <div id="cart-sidebar" class="fixed top-0 right-0 w-96 max-w-full h-full bg-white shadow-lg z-50 p-6 transform translate-x-full transition-transform duration-300" style="display:none;">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Your Cart</h2>
                <button id="cart-close-btn" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
            </div>
            <div id="cart-items-list" class="mb-4">
                <!-- Cart items will be rendered here -->
            </div>
            <div class="flex justify-between items-center border-t pt-4">
                <span class="font-bold">Total:</span>
                <span id="cart-total" class="text-lg font-bold text-blue-700">UGX 0.00</span>
            </div>
            <div class="mt-4">
                <label for="payment-method" class="block font-semibold mb-1">Payment Method</label>
                <select id="payment-method" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select payment method</option>
                    <option value="mobilemoney">Mobile Money</option>
                    <option value="bank">Bank</option>
                </select>
            </div>
            <button id="place-order-btn" class="mt-6 w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-bold">Place Order</button>
        </div>
    </main>
    <div id="retailer-toast" style="display:none;"></div>
@endsection 