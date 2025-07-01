@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-10 px-4">
    <div class="bg-white rounded-lg shadow p-8">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-blue-900">Add Raw Material</h1>
            <a href="{{ route('supplier.raw-material-inventory') }}" class="text-blue-600 hover:underline">&larr; Back to Inventory</a>
        </div>
        @if(session('success'))
            <div class="mb-4 text-green-700 bg-green-100 rounded p-3">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 text-red-700 bg-red-100 rounded p-3">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('supplier.add-raw-material') }}" class="space-y-5">
            @csrf
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Material Name</label>
                <input type="text" name="material_name" class="flex-1 border rounded px-3 py-2" required value="{{ old('material_name') }}">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Type</label>
                <select name="material_type" class="flex-1 border rounded px-3 py-2" required>
                    <option value="">Select type</option>
                    <option value="milk" @if(old('material_type')=='milk') selected @endif>Milk</option>
                    <option value="sugar" @if(old('material_type')=='sugar') selected @endif>Sugar</option>
                    <option value="fruit" @if(old('material_type')=='fruit') selected @endif>Fruit</option>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Batch Code</label>
                <input type="text" name="material_code" class="flex-1 border rounded px-3 py-2" required value="{{ old('material_code') }}">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Description</label>
                <textarea name="description" class="flex-1 border rounded px-3 py-2" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Quantity</label>
                <input type="number" name="quantity" class="flex-1 border rounded px-3 py-2" min="0.01" step="0.01" required value="{{ old('quantity') }}">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Unit of Measure</label>
                <select name="unit_of_measure" class="flex-1 border rounded px-3 py-2" required>
                    <option value="">Select unit</option>
                    <option value="liters" @if(old('unit_of_measure')=='liters') selected @endif>Liters</option>
                    <option value="kg" @if(old('unit_of_measure')=='kg') selected @endif>Kilograms</option>
                    <option value="grams" @if(old('unit_of_measure')=='grams') selected @endif>Grams</option>
                    <option value="pieces" @if(old('unit_of_measure')=='pieces') selected @endif>Pieces</option>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Unit Price</label>
                <input type="number" name="unit_price" class="flex-1 border rounded px-3 py-2" min="0" step="0.01" required value="{{ old('unit_price', 0) }}">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Harvest Date</label>
                <input type="date" name="harvest_date" class="flex-1 border rounded px-3 py-2" required value="{{ old('harvest_date') }}">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Expiry Date</label>
                <input type="date" name="expiry_date" class="flex-1 border rounded px-3 py-2" required value="{{ old('expiry_date') }}">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Quality Grade</label>
                <select name="quality_grade" class="flex-1 border rounded px-3 py-2" required>
                    <option value="">Select grade</option>
                    <option value="A" @if(old('quality_grade')=='A') selected @endif>Grade A</option>
                    <option value="B" @if(old('quality_grade')=='B') selected @endif>Grade B</option>
                    <option value="C" @if(old('quality_grade')=='C') selected @endif>Grade C</option>
                    <option value="D" @if(old('quality_grade')=='D') selected @endif>Grade D</option>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Temperature (°C)</label>
                <input type="number" name="temperature" class="flex-1 border rounded px-3 py-2" step="0.01" value="{{ old('temperature') }}">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">pH Level</label>
                <input type="number" name="ph_level" class="flex-1 border rounded px-3 py-2" min="0" max="14" step="0.1" value="{{ old('ph_level') }}">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Fat Content (%)</label>
                <input type="number" name="fat_content" class="flex-1 border rounded px-3 py-2" min="0" max="100" step="0.01" value="{{ old('fat_content') }}">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Protein Content (%)</label>
                <input type="number" name="protein_content" class="flex-1 border rounded px-3 py-2" min="0" max="100" step="0.01" value="{{ old('protein_content') }}">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Status</label>
                <select name="status" class="flex-1 border rounded px-3 py-2" required>
                    <option value="">Select status</option>
                    <option value="available" @if(old('status')=='available') selected @endif>Available</option>
                    <option value="in_use" @if(old('status')=='in_use') selected @endif>In Use</option>
                    <option value="expired" @if(old('status')=='expired') selected @endif>Expired</option>
                    <option value="disposed" @if(old('status')=='disposed') selected @endif>Disposed</option>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="sm:w-40 font-semibold text-gray-700">Quality Notes</label>
                <textarea name="quality_notes" class="flex-1 border rounded px-3 py-2" rows="3">{{ old('quality_notes') }}</textarea>
            </div>
            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded font-semibold">Add Raw Material</button>
            </div>
        </form>
    </div>
</div>
@endsection 