@extends('layouts.app')

@section('content')
<main class="main-content bg-gradient-to-br from-blue-50 via-green-50 to-white min-h-screen py-8">
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('retailer.cart.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">&larr; Back to Cart</a>
        <h1 class="text-4xl font-extrabold text-blue-700 mb-8 text-center tracking-tight drop-shadow">Retailer Checkout</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <form action="{{ route('retailer.checkout.store') }}" method="POST" class="bg-white rounded-2xl shadow-2xl p-8">
                @csrf
                <div class="mb-6">
                    <label for="delivery_address" class="block font-semibold mb-2 text-gray-700">Delivery Address *</label>
                    <input type="text" id="delivery_address" name="delivery_address" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-green-400" value="{{ old('delivery_address', Auth::user()->retailer->store_address ?? '') }}" required>
                    @error('delivery_address')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="delivery_contact" class="block font-semibold mb-2 text-gray-700">Delivery Contact Name *</label>
                    <input type="text" id="delivery_contact" name="delivery_contact" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-green-400" value="{{ old('delivery_contact', Auth::user()->retailer->store_manager ?? Auth::user()->name ?? '') }}" required>
                    @error('delivery_contact')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="delivery_phone" class="block font-semibold mb-2 text-gray-700">Delivery Phone *</label>
                    <input type="text" id="delivery_phone" name="delivery_phone" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-green-400" value="{{ old('delivery_phone', Auth::user()->retailer->store_phone ?? Auth::user()->phone ?? '') }}" required>
                    @error('delivery_phone')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="payment_method" class="block font-semibold mb-2 text-gray-700">Payment Method *</label>
                    <select id="payment_method" name="payment_method" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-green-400" required>
                        <option value="">Select Payment Method</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash on Delivery</option>
                        <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                    @error('payment_method')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="requested_delivery_date" class="block font-semibold mb-2 text-gray-700">Requested Delivery Date *</label>
                    <input type="date" id="requested_delivery_date" name="requested_delivery_date" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-green-400" value="{{ old('requested_delivery_date', date('Y-m-d', strtotime('+2 days'))) }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    @error('requested_delivery_date')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="special_instructions" class="block font-semibold mb-2 text-gray-700">Special Instructions (Optional)</label>
                    <textarea id="special_instructions" name="special_instructions" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-green-400" placeholder="Any special delivery instructions...">{{ old('special_instructions') }}</textarea>
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold text-lg shadow-lg transition">Place Order</button>
            </form>
            <div class="checkout-summary bg-white rounded-2xl shadow-2xl p-8">
                <h2 class="text-2xl font-bold mb-4 text-blue-800">Order Summary</h2>
                @foreach($cartItems as $item)
                    <div class="order-item flex items-center py-3 border-b border-gray-100">
                        <div>
                            @if($item->product->image_path)
                                <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->product_name }}" class="w-16 h-16 object-cover rounded-lg border">
                            @else
                                <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded-lg text-2xl text-gray-400">🥛</div>
                            @endif
                        </div>
                        <div class="order-item-details flex-1 ml-4">
                            <div class="order-item-name font-semibold">{{ $item->product->product_name }}</div>
                            <div class="order-item-price text-gray-600 text-sm">UGX {{ number_format($item->product->selling_price, 0) }} each</div>
                            <div class="order-item-quantity text-gray-600 text-sm">Qty: {{ $item->quantity }}</div>
                        </div>
                        <div class="order-item-total font-bold text-blue-700">UGX {{ number_format($item->quantity * $item->product->selling_price, 0) }}</div>
                    </div>
                @endforeach
                <div class="order-summary-total text-right mt-4 text-lg font-bold">Total: UGX {{ number_format($total, 0) }}</div>
            </div>
        </div>
    </div>
</main>
@endsection 