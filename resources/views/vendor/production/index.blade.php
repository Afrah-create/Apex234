@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="/css/vendor-production.css">
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Production Batches</h1>
    <form method="POST" action="{{ route('vendor.production.store') }}" class="mb-8">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Product</label>
            <select name="product_id" id="product_id" class="border rounded w-full p-2" required>
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
            <label class="block font-semibold mb-1">Number of Batches</label>
            <input type="number" name="batches" id="batches" class="border rounded w-full p-2" min="1" required>
        </div>
        <div id="raw-materials-required" class="mb-4 hidden bg-gray-50 border border-gray-200 rounded p-3">
            <h2 class="font-semibold mb-2">Total Raw Materials Required</h2>
            <ul id="raw-materials-list" class="list-disc pl-5"></ul>
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Produce</button>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pass raw material requirements for each product to JS
            const productRequirements = @json($products->mapWithKeys(function($product) {
                return [$product->id => $product->rawMaterialRequirements()];
            }));
            // Map material type to unit
            const materialUnits = { milk: 'liters', sugar: 'kg', fruit: 'kg' };
            const productSelect = document.getElementById('product_id');
            const batchesInput = document.getElementById('batches');
            const materialsSection = document.getElementById('raw-materials-required');
            const materialsList = document.getElementById('raw-materials-list');
            function updateMaterials() {
                const productId = productSelect.value;
                const batches = parseInt(batchesInput.value) || 0;
                if (!productId || !productRequirements[productId] || !batches) {
                    materialsSection.classList.add('hidden');
                    materialsList.innerHTML = '';
                    return;
                }
                const reqs = productRequirements[productId];
                materialsList.innerHTML = '';
                Object.entries(reqs).forEach(([type, perBatch]) => {
                    const total = perBatch * batches;
                    const unit = materialUnits[type] || '';
                    const li = document.createElement('li');
                    li.textContent = `${type.charAt(0).toUpperCase() + type.slice(1)}: ${total} ${unit}`;
                    materialsList.appendChild(li);
                });
                materialsSection.classList.remove('hidden');
            }
            productSelect.addEventListener('change', updateMaterials);
            batchesInput.addEventListener('input', updateMaterials);
        });
    </script>
</div>
@endsection 