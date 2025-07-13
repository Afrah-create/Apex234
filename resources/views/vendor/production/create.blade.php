@extends('layouts.vendor')
@section('content')
<link rel="stylesheet" href="/css/vendor-production.css">
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Create Production Batch</h1>
    <form method="POST" action="{{ route('vendor.production.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Product</label>
            <select name="product_id" class="border rounded w-full p-2" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->product_name ?? $product->name }}</option>
                @endforeach
            </select>
            @if($products->isEmpty())
                <div class="text-red-600 mt-2">No products available. Please contact admin to add products.</div>
            @endif
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Quantity Produced</label>
            <input type="number" name="quantity_produced" class="border rounded w-full p-2" min="1" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Raw Materials Used</label>
            <div id="raw-materials-list">
                @forelse($rawMaterials as $rm)
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="rm-{{ $rm->id }}" name="raw_materials[{{ $rm->id }}][id]" value="{{ $rm->id }}" class="mr-2" onchange="toggleQtyInput({{ $rm->id }})">
                        <span class="mr-2">{{ $rm->material_type }} ({{ $rm->quantity }} {{ $rm->unit_of_measure }})</span>
                        <input type="number" name="raw_materials[{{ $rm->id }}][quantity]" id="qty-{{ $rm->id }}" min="1" max="{{ $rm->quantity }}" class="border rounded w-24 p-1" placeholder="Qty" disabled>
                    </div>
                @empty
                    <div class="text-red-600">No raw materials available. Please add raw materials.</div>
                @endforelse
            </div>
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Record Batch</button>
    </form>
    <script>
        function toggleQtyInput(id) {
            const checkbox = document.getElementById('rm-' + id);
            const qtyInput = document.getElementById('qty-' + id);
            qtyInput.disabled = !checkbox.checked;
            if (!checkbox.checked) qtyInput.value = '';
        }
    </script>
</div>
@endsection 