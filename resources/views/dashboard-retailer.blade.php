@extends('layouts.app')

@section('content')
    <main class="main-content">
        <div class="flex justify-center mb-6">
            <div class="w-full max-w-xl bg-white border-2 border-green-500 rounded-2xl shadow-lg p-6 flex items-center gap-4">
                <svg class="w-10 h-10 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.657-1.343-3-3-3s-3 1.343-3 3 1.343 3 3 3 3-1.343 3-3zm0 0c0-1.657 1.343-3 3-3s3 1.343 3 3-1.343 3-3 3-3-1.343-3-3zm0 8v-2a4 4 0 00-4-4H5a2 2 0 00-2 2v2m16 0v-2a2 2 0 00-2-2h-3a4 4 0 00-4 4v2"/></svg>
                <div>
                    <div class="text-2xl md:text-3xl font-extrabold text-green-700 mb-1">You are most welcome,</div>
                    <div class="text-2xl md:text-3xl font-extrabold text-blue-700 tracking-wide">{{ strtoupper(auth()->user()->name) }}</div>
                </div>
            </div>
        </div>
        <h3 class="text-3xl font-extrabold bg-gradient-to-r from-blue-700 via-green-500 to-emerald-600 bg-clip-text text-transparent mb-2 tracking-tight drop-shadow-sm">Products Offered</h3>
        <div class="h-1 w-32 bg-gradient-to-r from-blue-400 via-green-400 to-emerald-400 rounded-full mb-6"></div>
       
        <div class="flex justify-end mb-4">
            <button id="cart-toggle-btn" class="relative flex items-center gap-2 px-6 py-2 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 shadow-lg hover:from-emerald-600 hover:to-green-700 active:scale-95 transition-all duration-150 font-bold text-white text-lg focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg class="w-7 h-7 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A1 1 0 007 17h10a1 1 0 00.95-.68L19 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7"></path></svg>
                <span>Cart</span>
                <span id="cart-count" class="absolute -top-2 -right-2 bg-white text-green-700 border-2 border-green-500 rounded-full px-2 py-0.5 text-xs font-bold shadow">0</span>
            </button>
        </div>
        <div class="grid grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="product-card" data-product-id="{{ $product->id }}">
                    <img src="{{ $product->image_path ? asset('storage/' . $product->image_path) : '/images/carousel/fresh-milk.jpg' }}" alt="{{ $product->product_name }}">
                    <div class="font-bold text-lg mb-2 text-orange-500 drop-shadow-sm">
                        {{ $product->product_name }}
                    </div>
                    <div class="text-orange-700 font-bold text-base mb-2">{{ $product->package_size ?? '200ml' }}</div>
                    <div class="mt-2 text-blue-600 font-bold">UGX {{ number_format($product->selling_price, 2) }}</div>
                    <div class="flex items-center gap-2 mt-3 w-full">
                        <input type="number" class="quantity-input border rounded px-2 py-1 w-20 text-center focus:ring-2 focus:ring-green-400" min="1" value="1" style="height:2.5rem;" />
                        <button class="add-to-cart-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold flex-1 transition-colors duration-150">Add to Cart</button>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Cart Sidebar/Modal -->
        <div id="cart-sidebar" class="fixed top-0 right-0 w-96 max-w-full h-full bg-gradient-to-b from-white via-gray-50 to-green-50 shadow-2xl z-50 p-0 transform translate-x-full transition-transform duration-300 rounded-l-3xl border-l-4 border-green-400" style="display:none;">
            <div class="flex flex-col h-full">
                <div class="flex justify-between items-center px-6 pt-6 pb-2 border-b border-green-100">
                    <h2 class="text-2xl font-extrabold text-green-700 tracking-tight">Your Cart</h2>
                    <button id="cart-close-btn" class="text-gray-400 hover:text-red-600 text-3xl font-bold transition-colors">&times;</button>
                </div>
                <div id="cart-items-list" class="flex-1 overflow-y-auto px-6 py-4">
                    <!-- Cart items will be rendered here -->
                </div>
                <div class="px-6 border-t border-green-100 pt-4 pb-2 bg-white/80">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-lg text-gray-700">Total:</span>
                        <span id="cart-total" class="text-2xl font-extrabold text-blue-700">UGX 0.00</span>
                    </div>
                    <div class="mt-4">
                        <label for="payment-method" class="block font-semibold mb-1 text-gray-700">Payment Method</label>
                        <select id="payment-method" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-green-400" required>
                            <option value="">Select payment method</option>
                            <option value="mobilemoney">Mobile Money</option>
                            <option value="bank">Bank</option>
                        </select>
                    </div>
                    <button id="place-order-btn" class="mt-6 w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-700 text-white px-4 py-3 rounded-xl font-bold text-lg shadow-lg transition-all duration-150">Place Order</button>
                </div>
            </div>
        </div>
    </main>
    <div id="retailer-toast" style="display:none;"></div>
@endsection

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endpush 