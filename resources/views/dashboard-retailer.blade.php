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
            <a href="{{ route('retailer.cart.index') }}" id="cart-toggle-btn" class="relative flex items-center gap-2 px-6 py-2 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 shadow-lg hover:from-emerald-600 hover:to-green-700 active:scale-95 transition-all duration-150 font-bold text-white text-lg focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg class="w-7 h-7 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A1 1 0 007 17h10a1 1 0 00.95-.68L19 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7"></path></svg>
                <span>Cart</span>
            </a>
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
                    <div class="mt-3 w-full">
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input type="number" name="quantity" value="1" min="1" class="w-16 p-1 border rounded mr-2">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold flex-1 transition-colors duration-150">Add to Cart</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
    <div id="retailer-toast" style="display:none;"></div>
@endsection 