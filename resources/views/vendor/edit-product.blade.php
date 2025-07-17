@extends('layouts.app')

@section('content')
<main class="main-content">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
        <a href="{{ route('vendor.manage-products') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Inventory
        </a>
    </div>
    <form id="edit-product-form" class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
        <input type="hidden" id="product-id" value="{{ $product->id }}">
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Product Name</label>
            <input type="text" name="product_name" class="w-full p-2 rounded border text-sm bg-gray-100" value="{{ $product->product_name }}" readonly>
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Product Type</label>
            <input type="text" name="product_type" class="w-full p-2 rounded border text-sm bg-gray-100" value="{{ $product->product_type }}" readonly>
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Selling Price (UGX)</label>
            <input type="number" name="selling_price" id="price" class="w-full p-2 rounded border text-sm bg-gray-100" value="{{ $product->selling_price }}" readonly>
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1 text-sm">Stock (Total)</label>
            <input type="number" name="stock" id="stock" class="w-full p-2 rounded border text-sm bg-gray-100" value="{{ $product->stock }}" min="0" readonly>
        </div>
        
        <!-- Inventory Management Section -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Inventory Management</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="mb-4">
                    <label class="block font-bold mb-1 text-sm text-green-700">Available Quantity</label>
                    <input type="number" name="quantity_available" id="quantity_available" class="w-full p-2 rounded border text-sm bg-gray-100" value="{{ $product->currentInventory->quantity_available ?? 0 }}" min="0" readonly>
                    <p class="text-xs text-gray-500 mt-1">Ready for sale</p>
                </div>
                
                <div class="mb-4">
                    <label class="block font-bold mb-1 text-sm text-orange-700">Reserved Quantity</label>
                    <input type="number" name="quantity_reserved" id="quantity_reserved" class="w-full p-2 rounded border text-sm" value="{{ $product->currentInventory->quantity_reserved ?? 0 }}" min="0">
                    <p class="text-xs text-gray-500 mt-1">Held for orders</p>
                </div>
                
                <div class="mb-4">
                    <label class="block font-bold mb-1 text-sm text-red-700">Damaged Quantity</label>
                    <input type="number" name="quantity_damaged" id="quantity_damaged" class="w-full p-2 rounded border text-sm" value="{{ $product->currentInventory->quantity_damaged ?? 0 }}" min="0">
                    <p class="text-xs text-gray-500 mt-1">Not sellable</p>
                </div>
            </div>
            
            <div id="stock-warning" class="text-red-600 font-bold text-sm hidden">The sum of Reserved and Damaged must not exceed the total stock.</div>
            
            <div class="mt-4 p-3 bg-blue-50 rounded border-l-4 border-blue-400">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-blue-800">Total Inventory:</span>
                    <span class="text-sm font-bold text-blue-900" id="total_inventory">0</span>
                </div>
                <div class="flex justify-between items-center mt-1">
                    <span class="text-sm font-medium text-blue-800">Inventory Value:</span>
                    <span class="text-sm font-bold text-blue-900" id="inventory_value">UGX 0.00</span>
                </div>
            </div>
        </div>
        
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg w-full text-base">Update Inventory</button>
        <div id="edit-product-success" class="mt-4 text-green-600 font-bold hidden text-sm">Inventory updated successfully!</div>
        <div id="edit-product-error" class="mt-4 text-red-600 font-bold hidden text-sm"></div>
    </form>
</main>
<script>
function recalculateProductAvailable() {
    const stock = parseInt(document.getElementById('stock').value) || 0;
    const reserved = parseInt(document.getElementById('quantity_reserved').value) || 0;
    const damaged = parseInt(document.getElementById('quantity_damaged').value) || 0;
    const sum = reserved + damaged;
    const available = stock - sum;
    document.getElementById('quantity_available').value = available >= 0 ? available : 0;
    const warning = document.getElementById('stock-warning');
    if (sum > stock) {
        warning.classList.remove('hidden');
        return false;
    } else {
        warning.classList.add('hidden');
        return true;
    }
}
['quantity_reserved', 'quantity_damaged'].forEach(id => {
    document.getElementById(id).addEventListener('input', recalculateProductAvailable);
});
document.getElementById('edit-product-form').addEventListener('submit', function(e) {
    if (!recalculateProductAvailable()) {
        e.preventDefault();
        return;
    }
    e.preventDefault();
    const id = {{ $product->currentInventory->id ?? 'null' }};
    const data = {
        quantity_reserved: document.getElementById('quantity_reserved').value,
        quantity_damaged: document.getElementById('quantity_damaged').value
    };
    fetch(`/api/vendor/inventory/products/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit-product-success').classList.remove('hidden');
            document.getElementById('edit-product-error').classList.add('hidden');
            setTimeout(() => {
                window.location.href = '{{ route("vendor.manage-products") }}';
            }, 2000);
        } else {
            document.getElementById('edit-product-error').textContent = data.message || 'Update failed.';
            document.getElementById('edit-product-error').classList.remove('hidden');
            document.getElementById('edit-product-success').classList.add('hidden');
        }
    })
    .catch(() => {
        document.getElementById('edit-product-error').textContent = 'An error occurred.';
        document.getElementById('edit-product-error').classList.remove('hidden');
        document.getElementById('edit-product-success').classList.add('hidden');
    });
});

function refreshAllData() {
    loadAnalyticsData();
    
    // Show refresh feedback
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Refreshed!';
    button.disabled = true;
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    }, 2000);
}

// Auto-calculate total value for products
function calculateTotalValue() {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const stock = parseInt(document.getElementById('stock').value) || 0;
    const totalValue = price * stock;
    document.getElementById('total_value').value = totalValue.toFixed(2);
}

// Auto-calculate inventory totals
function calculateInventoryTotals() {
    const available = parseInt(document.getElementById('quantity_available').value) || 0;
    const reserved = parseInt(document.getElementById('quantity_reserved').value) || 0;
    const damaged = parseInt(document.getElementById('quantity_damaged').value) || 0;
    const price = parseFloat(document.getElementById('price').value) || 0;
    
    const totalInventory = available + reserved + damaged;
    const inventoryValue = available * price; // Only available quantity contributes to value
    
    document.getElementById('total_inventory').textContent = totalInventory;
    document.getElementById('inventory_value').textContent = `UGX ${inventoryValue.toFixed(2)}`;
    
    // Auto-update inventory status based on quantities
    autoUpdateInventoryStatus(available, damaged);
}

// Auto-update inventory status based on quantities
function autoUpdateInventoryStatus(available, damaged) {
    const statusSelect = document.getElementById('inventory_status');
    let newStatus = 'available';
    
    if (damaged > 0 && available == 0) {
        newStatus = 'damaged';
    } else if (available == 0) {
        newStatus = 'out_of_stock';
    } else if (available <= 10) {
        newStatus = 'low_stock';
    } else {
        newStatus = 'available';
    }
    
    // Only update if the status would actually change
    if (statusSelect.value !== newStatus) {
        statusSelect.value = newStatus;
        
        // Add visual feedback
        const statusLabels = {
            'available': '✅ Available',
            'low_stock': '⚠️ Low Stock',
            'out_of_stock': '❌ Out of Stock',
            'expired': '⏰ Expired',
            'damaged': '💥 Damaged'
        };
        
        // Show a brief notification
       // showStatusNotification(`Status auto-updated to: ${statusLabels[newStatus]}`);
    }
}

// Show status notification
function showStatusNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm';
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Add event listeners for auto-calculation
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.getElementById('price');
    const stockInput = document.getElementById('stock');
    const availableInput = document.getElementById('quantity_available');
    const reservedInput = document.getElementById('quantity_reserved');
    const damagedInput = document.getElementById('quantity_damaged');
    
    if (priceInput && stockInput) {
        priceInput.addEventListener('input', calculateTotalValue);
        stockInput.addEventListener('input', calculateTotalValue);
    }
    
    if (availableInput && reservedInput && damagedInput && priceInput) {
        availableInput.addEventListener('input', calculateInventoryTotals);
        reservedInput.addEventListener('input', calculateInventoryTotals);
        damagedInput.addEventListener('input', calculateInventoryTotals);
        priceInput.addEventListener('input', calculateInventoryTotals);
        
        // Calculate initial values
        calculateInventoryTotals();
    }
});
</script>
@endsection 