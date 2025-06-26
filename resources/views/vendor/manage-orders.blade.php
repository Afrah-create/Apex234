@extends('layouts.app')

@section('content')
<main class="main-content">
    <h1>Manage Orders</h1>
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Order Raw Materials from Suppliers</h2>
        <form id="vendor-raw-material-order-form" class="bg-white rounded-lg shadow-md p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="material" class="block font-bold mb-1">Material</label>
                <select id="material" name="material" class="w-full p-2 rounded border">
                    <option value="milk">Milk</option>
                    <!-- More materials can be added here -->
                </select>
            </div>
            <div>
                <label for="quantity" class="block font-bold mb-1">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1" class="w-full p-2 rounded border" required>
            </div>
            <div>
                <label for="supplier" class="block font-bold mb-1">Supplier</label>
                <select id="supplier" name="supplier" class="w-full p-2 rounded border">
                    <!-- Populated by JS -->
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg w-full">Place Order</button>
            </div>
        </form>
        <div id="raw-material-order-success" class="mt-2 text-green-600 font-bold hidden">Order placed successfully!</div>
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">My Raw Material Orders</h2>
        <div class="bg-white rounded-lg shadow-md p-4 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Material</th>
                        <th>Quantity</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="vendor-raw-material-orders-list">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-4">Product Orders from Retailers</h2>
        <div class="bg-white rounded-lg shadow-md p-4 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Retailer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="vendor-product-orders-list">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</main>
<script>
// Populate suppliers dropdown
async function loadSuppliers() {
    const res = await fetch('/api/vendor/suppliers');
    const suppliers = await res.json();
    const select = document.getElementById('supplier');
    select.innerHTML = '';
    suppliers.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = s.name;
        select.appendChild(opt);
    });
}

// Handle raw material order form
const orderForm = document.getElementById('vendor-raw-material-order-form');
orderForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const material = document.getElementById('material').value;
    const quantity = document.getElementById('quantity').value;
    const supplier_id = document.getElementById('supplier').value;
    const res = await fetch('/api/vendor/raw-material-orders', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        body: JSON.stringify({ material, quantity, supplier_id })
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('raw-material-order-success').classList.remove('hidden');
        orderForm.reset();
        loadRawMaterialOrders();
        setTimeout(() => document.getElementById('raw-material-order-success').classList.add('hidden'), 2000);
    }
});

// List vendor's raw material orders
async function loadRawMaterialOrders() {
    const res = await fetch('/api/vendor/raw-material-orders');
    const orders = await res.json();
    const tbody = document.getElementById('vendor-raw-material-orders-list');
    tbody.innerHTML = '';
    orders.forEach(order => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${order.created_at.split('T')[0]}</td>
            <td>${order.material}</td>
            <td>${order.quantity}</td>
            <td>${order.supplier_name}</td>
            <td>${order.status}</td>
            <td></td>
        `;
        tbody.appendChild(tr);
    });
}

// List product orders from retailers
async function loadProductOrders() {
    const res = await fetch('/api/vendor/product-orders');
    const orders = await res.json();
    const tbody = document.getElementById('vendor-product-orders-list');
    tbody.innerHTML = '';
    orders.forEach(order => {
        order.items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${order.date}</td>
                <td>${order.retailer}</td>
                <td>${item.product}</td>
                <td>${item.quantity}</td>
                <td>${order.status}</td>
                <td>${order.status !== 'confirmed' ? `<button class='bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded' onclick='confirmProductOrder(${order.id})'>Confirm</button>` : ''}</td>
            `;
            tbody.appendChild(tr);
        });
    });
}

// Confirm retailer product order
async function confirmProductOrder(orderId) {
    await fetch(`/api/vendor/product-orders/${orderId}/confirm`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } });
    loadProductOrders();
}

// Initial load
window.addEventListener('DOMContentLoaded', function() {
    loadSuppliers();
    loadRawMaterialOrders();
    loadProductOrders();
});
</script>
@endsection 