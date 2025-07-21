@extends('layouts.app')

@section('content')
<main class="main-content bg-gradient-to-br from-blue-50 via-green-50 to-white min-h-screen py-8">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-4xl font-extrabold text-blue-700 mb-8 text-center tracking-tight drop-shadow">Your Cart</h2>
        <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-10">
            @if(session('success'))
                <div class="mb-4 text-green-700 bg-green-100 p-2 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 text-red-700 bg-red-100 p-2 rounded">{{ session('error') }}</div>
            @endif
            @if($cartItems->count())
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm rounded-xl">
                    <thead class="bg-gradient-to-r from-blue-200 via-green-100 to-emerald-100">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">Image</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">Product</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">Price</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">Quantity</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">Subtotal</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                        <tr class="border-b hover:bg-blue-50 transition">
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($item['product']->image_path)
                                    <img src="{{ asset('storage/' . $item['product']->image_path) }}" alt="{{ $item['product']->product_name }}" class="w-16 h-16 object-cover rounded-lg border">
                                @else
                                    <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded-lg text-2xl text-gray-400">🥛</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-semibold text-blue-700">{{ $item['product']->product_name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">UGX {{ number_format($item['product']->selling_price, 0) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <form action="{{ route('cart.update', $item['product']) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-16 p-1 border rounded">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">Update</button>
                                </form>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-bold text-emerald-700">UGX {{ number_format($item['subtotal'], 0) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <form action="{{ route('retailer.cart.remove', $item['product']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class="mt-6 text-right">
                    <span class="text-xl font-semibold">Total: UGX {{ number_format($total, 0) }}</span>
                </div>
                <div class="mt-8 flex justify-end gap-6">
                    <a href="{{ route('dashboard.retailer') }}" class="checkout-btn-secondary bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold shadow transition">Continue Shopping</a>
                    <a href="{{ route('retailer.checkout') }}" class="checkout-btn-primary bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold shadow transition">
                        Proceed to Checkout
                    </a>
                </div>
            @else
                <div class="text-center text-gray-500 py-12">
                    <svg class="mx-auto mb-4 w-16 h-16 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4a4 4 0 014 4v2M3 3h18v6H3V3zm0 8h18v10H3V11z"/></svg>
                    <div class="text-lg font-semibold">Your cart is empty!</div>
                    <a href="{{ route('dashboard.retailer') }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold shadow hover:bg-blue-700 transition">Start Shopping</a>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection 