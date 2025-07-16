@extends('layouts.app')

@section('content')
<main class="main-content">
   

    <!-- Raw Material Order Section -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4 text-blue-800">Order Raw Materials from Suppliers</h2>
        
        <!-- Available Materials Summary -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold mb-2">Available Raw Materials</h3>
            <div id="available-materials-summary" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Populated by JavaScript -->
            </div>
        </div>

        <!-- Supplier Details Modal -->
        <div id="supplier-modal-bg" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative mx-2">
                <button id="close-supplier-modal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <h2 id="supplier-modal-title" class="text-xl font-bold mb-4">Suppliers</h2>
                <div id="supplier-modal-content">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>

        <!-- Order Form -->
        <form id="vendor-raw-material-order-form" class="bg-white rounded-lg shadow-md p-6 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="material_type" class="block font-bold mb-1 text-sm">Material Type</label>
                    <select id="material_type" name="material_type" class="w-full p-2 rounded border text-sm" required>
                        <option value="">Select type</option>
                        <option value="milk">Milk</option>
                        <option value="sugar">Sugar</option>
                        <option value="fruit">Fruit</option>
                    </select>
                </div>
                <div>
                    <label for="material_name" class="block font-bold mb-1 text-sm">Material Name</label>
                    <input type="text" id="material_name" name="material_name" class="w-full p-2 rounded border text-sm" placeholder="e.g., Fresh Milk, White Sugar" required>
                </div>
                <div>
                    <label for="quantity" class="block font-bold mb-1 text-sm">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="0.01" step="0.01" class="w-full p-2 rounded border text-sm" required>
                    <div id="quantity-hint" class="text-xs text-gray-500 mt-1 hidden"></div>
                </div>
                <div>
                    <label for="unit_of_measure" class="block font-bold mb-1 text-sm">Unit</label>
                    <select id="unit_of_measure" name="unit_of_measure" class="w-full p-2 rounded border text-sm" required>
                        <option value="">Select unit</option>
                        <option value="liters">Liters</option>
                        <option value="kg">Kilograms</option>
                        <option value="grams">Grams</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="supplier_id" class="block font-bold mb-1 text-sm">Supplier</label>
                    <select id="supplier_id" name="supplier_id" class="w-full p-2 rounded border text-sm" required>
                        <option value="">Select supplier</option>
                    </select>
                </div>
                <div>
                    <label for="expected_delivery_date" class="block font-bold mb-1 text-sm">Expected Delivery Date</label>
                    <input type="date" id="expected_delivery_date" name="expected_delivery_date" class="w-full p-2 rounded border text-sm" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                    Place Order
                </button>
            </div>
        </form>
        
        <!-- Order Status Messages -->
        <div id="order-status-messages" class="mb-4">
            <div id="order-success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded hidden">
                <span id="success-message"></span>
            </div>
            <div id="order-error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden">
                <span id="error-message"></span>
            </div>
        </div>
    </div>

    <!-- Raw Material Orders Table -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4 text-blue-800">My Raw Material Orders</h2>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Order Date</th>
                            <th class="px-4 py-3 text-left font-semibold">Material</th>
                            <th class="px-4 py-3 text-left font-semibold">Quantity</th>
                            <th class="px-4 py-3 text-left font-semibold">Supplier</th>
                            <th class="px-4 py-3 text-left font-semibold">Total Amount</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Expected Delivery</th>
                            <th class="px-4 py-3 text-left font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vendor-raw-material-orders-list">
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">Loading orders...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="vendor-raw-material-orders-pagination" class="flex justify-center items-center py-4 space-x-4"></div>
        </div>
    </div>

    <!-- Product Orders from Retailers -->
    <div>
        <h2 class="text-xl font-semibold mb-4 text-blue-800">Product Orders from Retailers</h2>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Date</th>
                            <th class="px-4 py-3 text-left font-semibold">Retailer</th>
                            <th class="px-4 py-3 text-left font-semibold">Product</th>
                            <th class="px-4 py-3 text-left font-semibold">Quantity</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vendor-product-orders-list">
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">Loading orders...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
let availableMaterials = {};

// Load available raw materials
async function loadAvailableMaterials() {
    try {
        console.log('Loading available materials...');
        const res = await fetch('/api/vendor/available-raw-materials');
        console.log('Available materials response status:', res.status);
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const data = await res.json();
        console.log('Available materials loaded:', data);
        availableMaterials = data;
        renderAvailableMaterialsSummary();
        updateSupplierOptions();
    } catch (error) {
        console.error('Error loading available materials:', error);
    }
}

// Render available materials summary
function renderAvailableMaterialsSummary() {
    const container = document.getElementById('available-materials-summary');
    container.innerHTML = '';
    
    Object.keys(availableMaterials).forEach(type => {
        const material = availableMaterials[type];
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg p-3 shadow-sm border cursor-pointer hover:bg-blue-100 transition';
        card.setAttribute('data-type', type);
        card.innerHTML = `
            <div class="font-semibold text-sm capitalize">${type}</div>
            <div class="text-lg font-bold text-blue-600">${material.total_quantity}</div>
            <div class="text-xs text-gray-500">${material.suppliers.length} supplier(s)</div>
        `;
        card.addEventListener('click', function() {
            showSupplierModal(type, material.suppliers);
        });
        container.appendChild(card);
    });
}

// Show supplier modal with details
function showSupplierModal(type, suppliers) {
    const modalBg = document.getElementById('supplier-modal-bg');
    const modalTitle = document.getElementById('supplier-modal-title');
    const modalContent = document.getElementById('supplier-modal-content');
    modalTitle.textContent = `Suppliers for ${type.charAt(0).toUpperCase() + type.slice(1)}`;
    if (!suppliers || suppliers.length === 0) {
        modalContent.innerHTML = '<div class="text-gray-500">No suppliers available for this material.</div>';
    } else {
        modalContent.innerHTML = `
            <table class="min-w-full text-sm mb-2">
                <thead>
                    <tr class="bg-blue-50">
                        <th class="px-4 py-2 text-left">Supplier</th>
                        <th class="px-4 py-2 text-left">Available Quantity</th>
                        <th class="px-4 py-2 text-left">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    ${suppliers.map(s => `
                        <tr>
                            <td class="px-4 py-2">${s.supplier_name || '-'}</td>
                            <td class="px-4 py-2">${s.available_quantity}</td>
                            <td class="px-4 py-2">${s.unit_of_measure}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    }
    modalBg.classList.remove('hidden');
}

document.getElementById('close-supplier-modal').addEventListener('click', function() {
    document.getElementById('supplier-modal-bg').classList.add('hidden');
});
document.getElementById('supplier-modal-bg').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});

// Update supplier options based on selected material type
function updateSupplierOptions() {
    console.log('updateSupplierOptions called');
    const materialType = document.getElementById('material_type').value;
    const supplierSelect = document.getElementById('supplier_id');
    const unitSelect = document.getElementById('unit_of_measure');
    
    console.log('Material type selected:', materialType);
    
    // Always populate unit dropdown with all available units
    populateUnitDropdown();
    
    // Always populate supplier dropdown with all suppliers
    loadAllSuppliers();
    
    if (materialType && availableMaterials[materialType]) {
        const material = availableMaterials[materialType];
        console.log('Available material data:', material);
        
        // Auto-fill material name based on type
        const materialNameInput = document.getElementById('material_name');
        if (materialNameInput && !materialNameInput.value) {
            const materialNames = {
                'milk': 'Fresh Milk',
                'sugar': 'White Sugar',
                'fruit': 'Fresh Fruit'
            };
            materialNameInput.value = materialNames[materialType] || materialType.charAt(0).toUpperCase() + materialType.slice(1);
        }
    }
}

// Populate unit dropdown with all available units
function populateUnitDropdown() {
    const unitSelect = document.getElementById('unit_of_measure');
    unitSelect.innerHTML = '<option value="">Select unit</option>';
    
    // Add all possible units
    const units = ['liters', 'kg', 'grams'];
    units.forEach(unit => {
        const option = document.createElement('option');
        option.value = unit;
        option.textContent = unit.charAt(0).toUpperCase() + unit.slice(1);
        unitSelect.appendChild(option);
    });
}

// Load all suppliers from the database
async function loadAllSuppliers() {
    try {
        console.log('Loading suppliers...');
        const res = await fetch('/api/vendor/suppliers');
        console.log('Response status:', res.status);
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const suppliers = await res.json();
        console.log('Suppliers loaded:', suppliers);
        
        const supplierSelect = document.getElementById('supplier_id');
        
        // Clear and populate supplier dropdown
        supplierSelect.innerHTML = '<option value="">Select supplier</option>';
        suppliers.forEach(supplier => {
            const option = document.createElement('option');
            option.value = supplier.id;
            option.textContent = supplier.name;
            supplierSelect.appendChild(option);
        });
        
        console.log('Supplier dropdown populated with', suppliers.length, 'suppliers');
    } catch (error) {
        console.error('Error loading suppliers:', error);
        // Show error in the dropdown
        const supplierSelect = document.getElementById('supplier_id');
        supplierSelect.innerHTML = '<option value="">Error loading suppliers</option>';
    }
}

// Handle raw material order form
const orderForm = document.getElementById('vendor-raw-material-order-form');
orderForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Client-side validation for quantity
    const quantity = parseFloat(document.getElementById('quantity').value);
    const maxAvailable = document.getElementById('quantity').getAttribute('data-max-available');
    
    if (maxAvailable && quantity > parseFloat(maxAvailable)) {
        showOrderMessage('error', `Quantity exceeds available inventory. Maximum available: ${maxAvailable}`);
        return;
    }
    
    const formData = {
        material_type: document.getElementById('material_type').value,
        material_name: document.getElementById('material_name').value,
        quantity: quantity,
        supplier_id: document.getElementById('supplier_id').value,
        unit_of_measure: document.getElementById('unit_of_measure').value,
        expected_delivery_date: document.getElementById('expected_delivery_date').value,
    };

    try {
        const res = await fetch('/api/vendor/raw-material-orders', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            },
            body: JSON.stringify(formData)
        });
        
        const data = await res.json();
        
        if (data.success) {
            showOrderMessage('success', data.message);
            orderForm.reset();
            clearAvailabilityMessage();
            loadRawMaterialOrders();
            loadAvailableMaterials();
        } else {
            showOrderMessage('error', data.message);
        }
    } catch (error) {
        showOrderMessage('error', 'An error occurred while placing the order.');
    }
});

// Show order status messages
function showOrderMessage(type, message) {
    const successDiv = document.getElementById('order-success');
    const errorDiv = document.getElementById('order-error');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    
    if (type === 'success') {
        successMessage.textContent = message;
        successDiv.classList.remove('hidden');
        errorDiv.classList.add('hidden');
        setTimeout(() => successDiv.classList.add('hidden'), 5000);
    } else {
        errorMessage.textContent = message;
        errorDiv.classList.remove('hidden');
        successDiv.classList.add('hidden');
        setTimeout(() => errorDiv.classList.add('hidden'), 5000);
    }
}

// Remove archive/unarchive buttons and add pagination
let vendorRawMaterialOrdersCurrentPage = 1;
let vendorRawMaterialOrdersLastPage = 1;

async function loadRawMaterialOrders(page = 1) {
    try {
        const res = await fetch(`/api/vendor/raw-material-orders?page=${page}`);
        const result = await res.json();
        const orders = result.data;
        vendorRawMaterialOrdersCurrentPage = result.current_page;
        vendorRawMaterialOrdersLastPage = result.last_page;
        const tbody = document.getElementById('vendor-raw-material-orders-list');
        if (orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-500">No orders found</td></tr>';
            renderVendorRawMaterialOrdersPagination();
            return;
        }
        tbody.innerHTML = '';
        orders.forEach(order => {
            const tr = document.createElement('tr');
            tr.className = 'border-b hover:bg-gray-50';
            tr.innerHTML = `
                <td class="px-4 py-3">${new Date(order.order_date).toLocaleDateString()}</td>
                <td class="px-4 py-3">
                    <div class="font-medium">${order.material_name}</div>
                    <div class="text-xs text-gray-500 capitalize">${order.material_type}</div>
                </td>
                <td class="px-4 py-3">${order.quantity} ${order.unit_of_measure}</td>
                <td class="px-4 py-3">
                    <div class="font-medium">${order.supplier_name}</div>
                    <div class="text-xs text-gray-500">${order.supplier_email}</div>
                </td>
                <td class="px-4 py-3">UGX ${parseFloat(order.total_amount).toLocaleString()}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold ${getStatusColor(order.status)}">
                        ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                    </span>
                </td>
                <td class="px-4 py-3">${order.expected_delivery_date || '-'}</td>
                <td class="px-4 py-3">
                    ${order.status === 'pending' || order.status === 'confirmed' ? 
                        `<button onclick="cancelOrder(${order.id})" class="text-red-600 hover:text-red-800 text-sm">Cancel</button>` : 
                        ''
                    }
                </td>
            `;
            tbody.appendChild(tr);
        });
        renderVendorRawMaterialOrdersPagination();
    } catch (error) {
        console.error('Error loading raw material orders:', error);
    }
}

function renderVendorRawMaterialOrdersPagination() {
    let pagination = document.getElementById('vendor-raw-material-orders-pagination');
    if (!pagination) {
        pagination = document.createElement('div');
        pagination.id = 'vendor-raw-material-orders-pagination';
        pagination.className = 'flex justify-center items-center py-4 space-x-4';
        document.querySelector('.mb-8 .bg-white').appendChild(pagination);
    }
    pagination.innerHTML = '';
    if (vendorRawMaterialOrdersLastPage <= 1) return;
    const prevBtn = document.createElement('button');
    prevBtn.textContent = 'Previous';
    prevBtn.className = 'px-3 py-1 rounded bg-gray-200 hover:bg-gray-300';
    prevBtn.disabled = vendorRawMaterialOrdersCurrentPage === 1;
    prevBtn.onclick = () => loadRawMaterialOrders(vendorRawMaterialOrdersCurrentPage - 1);
    const nextBtn = document.createElement('button');
    nextBtn.textContent = 'Next';
    nextBtn.className = 'px-3 py-1 rounded bg-gray-200 hover:bg-gray-300';
    nextBtn.disabled = vendorRawMaterialOrdersCurrentPage === vendorRawMaterialOrdersLastPage;
    nextBtn.onclick = () => loadRawMaterialOrders(vendorRawMaterialOrdersCurrentPage + 1);
    const pageInfo = document.createElement('span');
    pageInfo.textContent = `Page ${vendorRawMaterialOrdersCurrentPage} of ${vendorRawMaterialOrdersLastPage}`;
    pagination.appendChild(prevBtn);
    pagination.appendChild(pageInfo);
    pagination.appendChild(nextBtn);
}

// Get status color for badges
function getStatusColor(status) {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'confirmed': 'bg-blue-100 text-blue-800',
        'processing': 'bg-purple-100 text-purple-800',
        'shipped': 'bg-indigo-100 text-indigo-800',
        'delivered': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
        'unavailable': 'bg-gray-100 text-gray-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

// Cancel order
async function cancelOrder(orderId) {
    (async function() {
        const confirmed = await showConfirmModal('Are you sure you want to cancel this order?', 'Cancel Order');
        if (!confirmed) return;
        try {
            const res = await fetch(`/api/vendor/raw-material-orders/${orderId}/cancel`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                }
            });
            
            const data = await res.json();
            
            if (data.success) {
                showOrderMessage('success', data.message);
                loadRawMaterialOrders();
            } else {
                showOrderMessage('error', data.message);
            }
        } catch (error) {
            showOrderMessage('error', 'An error occurred while cancelling the order.');
        }
    })();
}

// List product orders from retailers
async function loadProductOrders() {
    try {
        const res = await fetch('/api/vendor/product-orders');
        let orders = await res.json();
        // Sort orders by date descending (most recent first)
        orders.sort((a, b) => (b.date > a.date ? 1 : b.date < a.date ? -1 : 0));
        const tbody = document.getElementById('vendor-product-orders-list');
        if (orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">No product orders found</td></tr>';
            return;
        }
        tbody.innerHTML = '';
        orders.forEach(order => {
            order.items.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'border-b hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="px-4 py-3">${order.date}</td>
                    <td class="px-4 py-3">${order.retailer || order.order_source}</td>
                    <td class="px-4 py-3">${item.product}</td>
                    <td class="px-4 py-3">${item.quantity}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold ${getStatusColor(order.status)}">
                            ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        ${order.status !== 'confirmed' ? 
                            `<button onclick="confirmProductOrder(${order.id})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">Confirm</button>` : 
                            '-'
                        }
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
    } catch (error) {
        console.error('Error loading product orders:', error);
    }
}

// Confirm retailer product order
async function confirmProductOrder(orderId) {
    try {
        const res = await fetch(`/api/vendor/product-orders/${orderId}/confirm`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            }
        });
        
        const data = await res.json();
        
        if (data.success) {
            showOrderMessage('success', 'Order confirmed successfully!');
            loadProductOrders();
        } else {
            showOrderMessage('error', 'Failed to confirm order.');
        }
    } catch (error) {
        showOrderMessage('error', 'An error occurred while confirming the order.');
    }
}

// Event listeners
document.getElementById('material_type').addEventListener('change', updateSupplierOptions);

// Quantity input validation
document.getElementById('quantity').addEventListener('input', function() {
    const quantity = parseFloat(this.value);
    const maxAvailable = this.getAttribute('data-max-available');
    
    if (maxAvailable && quantity > parseFloat(maxAvailable)) {
        this.setCustomValidity(`Maximum available: ${maxAvailable}`);
        this.classList.add('border-red-500');
    } else {
        this.setCustomValidity('');
        this.classList.remove('border-red-500');
    }
});

// When supplier is selected, show availability information
document.getElementById('supplier_id').addEventListener('change', function() {
    const materialType = document.getElementById('material_type').value;
    const supplierId = this.value;
    const quantityInput = document.getElementById('quantity');
    
    // Clear any previous availability messages
    clearAvailabilityMessage();
    
    if (materialType && supplierId && availableMaterials[materialType]) {
        const material = availableMaterials[materialType];
        const selectedSupplier = material.suppliers.find(s => s.supplier_id == supplierId);
        
        if (selectedSupplier) {
            // This supplier has the selected material type available
            showAvailabilityMessage(`Available: ${selectedSupplier.available_quantity} ${selectedSupplier.unit_of_measure}`, 'success');
            
            // Set max attribute on quantity input
            quantityInput.setAttribute('max', selectedSupplier.available_quantity);
            quantityInput.setAttribute('data-max-available', selectedSupplier.available_quantity);
            
            // Show quantity hint
            showQuantityHint(`Maximum available: ${selectedSupplier.available_quantity} ${selectedSupplier.unit_of_measure}`);
        } else {
            // This supplier doesn't have the selected material type available
            showAvailabilityMessage('No availability for this material type', 'error');
            quantityInput.removeAttribute('max');
            quantityInput.removeAttribute('data-max-available');
            hideQuantityHint();
        }
    } else if (materialType && supplierId) {
        // Material type selected but no availability data
        showAvailabilityMessage('Check availability', 'warning');
        quantityInput.removeAttribute('max');
        quantityInput.removeAttribute('data-max-available');
        hideQuantityHint();
    } else {
        quantityInput.removeAttribute('max');
        quantityInput.removeAttribute('data-max-available');
        hideQuantityHint();
    }
});

// Show availability message
function showAvailabilityMessage(message, type) {
    clearAvailabilityMessage();
    
    const messageDiv = document.createElement('div');
    messageDiv.id = 'availability-message';
    
    let className = 'px-3 py-2 rounded text-sm font-medium ';
    if (type === 'success') {
        className += 'bg-green-100 text-green-800 border border-green-200';
    } else if (type === 'error') {
        className += 'bg-red-100 text-red-800 border border-red-200';
    } else if (type === 'warning') {
        className += 'bg-yellow-100 text-yellow-800 border border-yellow-200';
    }
    
    messageDiv.className = className;
    messageDiv.textContent = message;
    
    // Insert after the supplier dropdown
    const supplierDiv = document.getElementById('supplier_id').closest('div');
    supplierDiv.parentNode.insertBefore(messageDiv, supplierDiv.nextSibling);
}

// Clear availability message
function clearAvailabilityMessage() {
    const existingMessage = document.getElementById('availability-message');
    if (existingMessage) {
        existingMessage.remove();
    }
}

// Show quantity hint
function showQuantityHint(message) {
    const hintDiv = document.getElementById('quantity-hint');
    hintDiv.textContent = message;
    hintDiv.classList.remove('hidden');
}

// Hide quantity hint
function hideQuantityHint() {
    const hintDiv = document.getElementById('quantity-hint');
    hintDiv.classList.add('hidden');
}

// Initial load
window.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, starting initialization...');
    loadAvailableMaterials();
    loadAllSuppliers();
    loadRawMaterialOrders();
    loadProductOrders();
});
</script>
@endsection 