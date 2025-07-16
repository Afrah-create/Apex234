@extends('layouts.vendor')
@section('content')
<link rel="stylesheet" href="/css/vendor-production.css">
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Create Production Batch</h1>
    <form method="POST" action="{{ route('vendor.production.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Product</label>
            <select name="product_id" id="product_id" class="border rounded w-full p-2" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->product_name ?? $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Number of Batches</label>
            <input type="number" name="batches" id="batches" class="border rounded w-full p-2" min="1" required>
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Produce</button>
    </form>
</div>
@endsection 