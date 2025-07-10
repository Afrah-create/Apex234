@extends('layouts.app')

@section('content')
<main class="main-content">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Raw Material</h1>
        <a href="{{ route('vendor.manage-products') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Inventory
        </a>
    </div>
    <form id="edit-raw-material-form" class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
        <input type="hidden" id="raw-material-id" value="{{ $rawMaterial->id }}">
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Material Name</label>
            <input type="text" name="material_name" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->material_name }}">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Material Type</label>
            <input type="text" name="material_type" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->material_type }}">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Quantity (Delivered)</label>
            <input type="number" name="quantity" id="quantity" class="w-full p-2 rounded border text-sm bg-gray-100" value="{{ $rawMaterial->quantity }}" min="0" step="0.01" readonly required>
        </div>
        <!-- Inventory Management Section -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Inventory Management</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="mb-4">
                    <label class="block font-bold mb-1 text-sm text-green-700">Available Quantity</label>
                    <input type="number" name="available" id="available" class="w-full p-2 rounded border text-sm bg-gray-100" value="{{ $rawMaterial->available ?? 0 }}" min="0" readonly>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-1 text-sm text-blue-700">In Use Quantity</label>
                    <input type="number" name="in_use" id="in_use" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->in_use ?? 0 }}" min="0">
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-1 text-sm text-red-700">Expired Quantity</label>
                    <input type="number" name="expired" id="expired" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->expired ?? 0 }}" min="0">
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-1 text-sm text-gray-700">Disposed Quantity</label>
                    <input type="number" name="disposed" id="disposed" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->disposed ?? 0 }}" min="0">
                </div>
            </div>
            <div id="quantity-warning" class="text-red-600 font-bold text-sm hidden">The sum of In Use, Expired, and Disposed must not exceed the delivered quantity.</div>
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Unit of Measure</label>
            <input type="text" name="unit_of_measure" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->unit_of_measure }}">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Unit Price (UGX)</label>
            <input type="number" name="unit_price" id="unit_price" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->unit_price }}" min="0" step="0.01" required>
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Total Cost (UGX)</label>
            <input type="number" name="total_cost" id="total_cost" class="w-full p-2 rounded border text-sm bg-gray-100" value="{{ $rawMaterial->total_cost }}" readonly>
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Harvest Date</label>
            <input type="date" name="harvest_date" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->harvest_date }}">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Expiry Date</label>
            <input type="date" name="expiry_date" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->expiry_date }}">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Quality Grade</label>
            <input type="text" name="quality_grade" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->quality_grade }}">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Temperature (°C)</label>
            <input type="number" name="temperature" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->temperature }}" step="0.01">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">pH Level</label>
            <input type="number" name="ph_level" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->ph_level }}" step="0.01">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Fat Content (%)</label>
            <input type="number" name="fat_content" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->fat_content }}" step="0.01">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Protein Content (%)</label>
            <input type="number" name="protein_content" class="w-full p-2 rounded border text-sm" value="{{ $rawMaterial->protein_content }}" step="0.01">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Status</label>
            <select name="status" class="w-full p-2 rounded border text-sm">
                <option value="available" {{ $rawMaterial->status == 'available' ? 'selected' : '' }}>Available</option>
                <option value="in_use" {{ $rawMaterial->status == 'in_use' ? 'selected' : '' }}>In Use</option>
                <option value="expired" {{ $rawMaterial->status == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="disposed" {{ $rawMaterial->status == 'disposed' ? 'selected' : '' }}>Disposed</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Quality Notes</label>
            <textarea name="quality_notes" class="w-full p-2 rounded border text-sm" rows="2">{{ $rawMaterial->quality_notes }}</textarea>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg w-full text-base">Update Raw Material</button>
        <div id="edit-raw-material-success" class="mt-4 text-green-600 font-bold hidden text-sm">Raw material updated successfully!</div>
        <div id="edit-raw-material-error" class="mt-4 text-red-600 font-bold hidden text-sm"></div>
    </form>
</main>
<script>
function recalculateAvailable() {
    const delivered = parseFloat(document.getElementById('quantity').value) || 0;
    const inUse = parseFloat(document.getElementById('in_use').value) || 0;
    const expired = parseFloat(document.getElementById('expired').value) || 0;
    const disposed = parseFloat(document.getElementById('disposed').value) || 0;
    const sum = inUse + expired + disposed;
    const available = delivered - sum;
    document.getElementById('available').value = available >= 0 ? available : 0;
    const warning = document.getElementById('quantity-warning');
    if (sum > delivered) {
        warning.classList.remove('hidden');
        return false;
    } else {
        warning.classList.add('hidden');
        return true;
    }
}
['in_use', 'expired', 'disposed'].forEach(id => {
    document.getElementById(id).addEventListener('input', recalculateAvailable);
});
document.getElementById('edit-raw-material-form').addEventListener('submit', function(e) {
    if (!recalculateAvailable()) {
    e.preventDefault();
    }
    
    const form = e.target;
    const id = document.getElementById('raw-material-id').value;
    const formData = new FormData(form);
    
    // Convert FormData to JSON for better compatibility with PUT requests
    const jsonData = {};
    formData.forEach((value, key) => {
        if (value !== '') { // Only include non-empty values
            jsonData[key] = value;
        }
    });
    
    fetch(`/api/vendor/inventory/raw-materials/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(jsonData)
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => {
                throw new Error(err.message || 'Update failed');
            });
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            document.getElementById('edit-raw-material-success').classList.remove('hidden');
            document.getElementById('edit-raw-material-error').classList.add('hidden');
            // Redirect back to inventory page after 2 seconds
            setTimeout(() => {
                window.location.href = '{{ route("vendor.manage-products") }}';
            }, 2000);
        } else {
            throw new Error(data.message || 'Update failed');
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        document.getElementById('edit-raw-material-error').textContent = error.message || 'An error occurred.';
        document.getElementById('edit-raw-material-error').classList.remove('hidden');
        document.getElementById('edit-raw-material-success').classList.add('hidden');
    });
});

function exportReport(type) {
    // Simulate report export
    const reports = {
        sales: 'Sales Report exported to PDF',
        inventory: 'Inventory Report exported to Excel',
        analytics: 'Analytics Report exported to PDF',
        ml: 'ML Insights exported to JSON'
    };
    
    alert(reports[type] || 'Report exported successfully');
}

// Auto-calculate total cost for raw materials
function calculateTotalCost() {
    const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const totalCost = unitPrice * quantity;
    document.getElementById('total_cost').value = totalCost.toFixed(2);
}

// Add event listeners for auto-calculation
document.addEventListener('DOMContentLoaded', function() {
    const unitPriceInput = document.getElementById('unit_price');
    const quantityInput = document.getElementById('quantity');
    
    if (unitPriceInput && quantityInput) {
        unitPriceInput.addEventListener('input', calculateTotalCost);
        quantityInput.addEventListener('input', calculateTotalCost);
    }
});
</script>
@endsection 