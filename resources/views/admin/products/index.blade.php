@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Product Management</h2>
        <p class="text-gray-600 text-lg">View, edit, and manage all yoghurt products. Update prices, names, discounts, and images easily.</p>
    </div>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-6 border border-green-300">{{ session('success') }}</div>
    @endif
    <div class="bg-white shadow-lg rounded-xl overflow-x-auto p-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Price (UGX)</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Discount (%)</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($products as $product)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 align-middle">
                        <div class="flex items-center justify-center">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->product_name }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200 shadow-sm">
                            @else
                                <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 text-3xl text-gray-300">🧴</div>
                            @endif
                        </div>
                    </td>
                    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                        @csrf
                        <td class="px-6 py-4 align-middle">
                            <input name="product_name" value="{{ $product->product_name }}" class="border border-gray-300 rounded-lg px-3 py-2 w-44 focus:ring-2 focus:ring-blue-400 focus:outline-none text-gray-900" required />
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <input name="selling_price" value="{{ $product->selling_price }}" type="number" step="0.01" min="0" class="border border-gray-300 rounded-lg px-3 py-2 w-28 focus:ring-2 focus:ring-blue-400 focus:outline-none text-gray-900" required />
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <input name="discount" value="{{ $product->discount ?? '' }}" type="number" step="0.01" min="0" max="100" class="border border-gray-300 rounded-lg px-3 py-2 w-20 focus:ring-2 focus:ring-blue-400 focus:outline-none text-gray-900" placeholder="0" />
                        </td>
                        <td class="px-6 py-4 align-middle text-gray-700">{{ $product->product_type }}</td>
                        <td class="px-6 py-4 align-middle text-gray-700">{{ $product->stock }}</td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex flex-col gap-2 items-center">
                                <input type="file" name="image" accept="image/*" class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition">Save</button>
                            </div>
                        </td>
                    </form>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection 