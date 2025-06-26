@extends('layouts.app')

@section('content')
<main class="main-content">
    <h1>Manage Products</h1>
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Add New Product</h2>
        <form id="vendor-add-product-form" class="bg-white rounded-lg shadow-md p-6 grid grid-cols-1 md:grid-cols-5 gap-4" enctype="multipart/form-data">
            <div>
                <label for="product-image" class="block font-bold mb-1">Image</label>
                <input type="file" id="product-image" name="image" accept="image/*" class="w-full p-2 rounded border">
            </div>
            <div>
                <label for="product-name" class="block font-bold mb-1">Name</label>
                <input type="text" id="product-name" name="name" class="w-full p-2 rounded border" required>
            </div>
            <div>
                <label for="product-type" class="block font-bold mb-1">Type</label>
                <select id="product-type" name="type" class="w-full p-2 rounded border">
                    <option value="greek">Greek</option>
                    <option value="low_fat">Low Fat</option>
                    <option value="organic">Organic</option>
                    <option value="plain">Plain</option>
                    <option value="flavored">Flavored</option>
                </select>
            </div>
            <div>
                <label for="product-price" class="block font-bold mb-1">Price</label>
                <input type="number" id="product-price" name="price" min="0" step="0.01" class="w-full p-2 rounded border" required>
            </div>
            <div>
                <label for="product-stock" class="block font-bold mb-1">Stock</label>
                <input type="number" id="product-stock" name="stock" min="0" class="w-full p-2 rounded border" required>
            </div>
            <div class="md:col-span-5 flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg w-full">Add Product</button>
            </div>
        </form>
        <div id="add-product-success" class="mt-2 text-green-600 font-bold hidden">Product added successfully!</div>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-4">My Products</h2>
        <div class="bg-white rounded-lg shadow-md p-4 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="vendor-products-list">
                    <!-- Example row with images -->
                    <tr>
                        <td><img src="/images/greek.jpeg" alt="Greek Yoghurt" class="h-16 w-16 object-cover rounded" /></td>
                        <td>Greek Vanilla Yoghurt</td>
                        <td>Greek</td>
                        <td><span class="text-green-600 font-bold">Active</span></td>
                        <td>$1.50</td>
                        <td>120</td>
                        <td>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded mr-2">Edit</button>
                            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded mr-2">Delete</button>
                            <button class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded">Deactivate</button>
                        </td>
                    </tr>
                    <tr>
                        <td><img src="/images/strawbeery.jpeg" alt="Organic Strawberry Yoghurt" class="h-16 w-16 object-cover rounded" /></td>
                        <td>Organic Strawberry Yoghurt</td>
                        <td>Organic</td>
                        <td><span class="text-green-600 font-bold">Active</span></td>
                        <td>$1.60</td>
                        <td>80</td>
                        <td>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded mr-2">Edit</button>
                            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded mr-2">Delete</button>
                            <button class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded">Deactivate</button>
                        </td>
                    </tr>
                    <tr>
                        <td><img src="/images/mango (2).jpeg" alt="Low Fat Blueberry Yoghurt" class="h-16 w-16 object-cover rounded" /></td>
                        <td>Low Fat Blueberry Yoghurt</td>
                        <td>Low Fat</td>
                        <td><span class="text-green-600 font-bold">Active</span></td>
                        <td>$1.40</td>
                        <td>100</td>
                        <td>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded mr-2">Edit</button>
                            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded mr-2">Delete</button>
                            <button class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded">Deactivate</button>
                        </td>
                    </tr>
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Product Modal (hidden by default) -->
    <div id="edit-product-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg relative">
            <button onclick="closeEditProductModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-900">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Edit Product</h2>
            <form id="vendor-edit-product-form" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="edit-product-id">
                <div>
                    <label for="edit-product-image" class="block font-bold mb-1">Image</label>
                    <img id="edit-product-image-preview" src="" alt="Product Image" class="h-16 w-16 object-cover rounded mb-2" />
                    <input type="file" id="edit-product-image" name="image" accept="image/*" class="w-full p-2 rounded border">
                </div>
                <div>
                    <label for="edit-product-name" class="block font-bold mb-1">Name</label>
                    <input type="text" id="edit-product-name" name="name" class="w-full p-2 rounded border" required>
                </div>
                <div>
                    <label for="edit-product-type" class="block font-bold mb-1">Type</label>
                    <select id="edit-product-type" name="type" class="w-full p-2 rounded border">
                        <option value="greek">Greek</option>
                        <option value="low_fat">Low Fat</option>
                        <option value="organic">Organic</option>
                        <option value="plain">Plain</option>
                        <option value="flavored">Flavored</option>
                    </select>
                </div>
                <div>
                    <label for="edit-product-price" class="block font-bold mb-1">Price</label>
                    <input type="number" id="edit-product-price" name="price" min="0" step="0.01" class="w-full p-2 rounded border" required>
                </div>
                <div>
                    <label for="edit-product-stock" class="block font-bold mb-1">Stock</label>
                    <input type="number" id="edit-product-stock" name="stock" min="0" class="w-full p-2 rounded border" required>
                </div>
                <div class="md:col-span-2 flex items-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg w-full">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
// Helper to get CSRF token
function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

// Fetch and display products
async function loadVendorProducts() {
    const res = await fetch('/api/vendor/products');
    const products = await res.json();
    const tbody = document.getElementById('vendor-products-list');
    tbody.innerHTML = '';
    products.forEach(product => {
        const tr = document.createElement('tr');
        let imgSrc = product.image_path ? `/storage/${product.image_path}` : (product.product_type === 'greek' ? '/images/greek.jpeg' : product.product_type === 'organic' ? '/images/strawbeery.jpeg' : '/images/mango (2).jpeg');
        tr.innerHTML = `
            <td><img src="${imgSrc}" alt="${product.product_name}" class="h-16 w-16 object-cover rounded" /></td>
            <td>${product.product_name}</td>
            <td>${product.product_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
            <td><span class="${product.status === 'active' ? 'text-green-600' : 'text-gray-600'} font-bold">${product.status.charAt(0).toUpperCase() + product.status.slice(1)}</span></td>
            <td>$${parseFloat(product.selling_price).toFixed(2)}</td>
            <td>${product.stock ?? '-'}</td>
            <td>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded mr-2" onclick="openEditProductModal(${product.id})">Edit</button>
                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded mr-2" onclick="deleteProduct(${product.id})">Delete</button>
                <button class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded" onclick="toggleProductStatus(${product.id})">${product.status === 'active' ? 'Deactivate' : 'Activate'}</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Add product form
const addForm = document.getElementById('vendor-add-product-form');
addForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(addForm);
    const res = await fetch('/api/vendor/products', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCSRFToken() },
        body: formData
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('add-product-success').classList.remove('hidden');
        addForm.reset();
        loadVendorProducts();
        setTimeout(() => document.getElementById('add-product-success').classList.add('hidden'), 2000);
    }
});

// Edit product modal logic
let currentEditProductId = null;
function openEditProductModal(id) {
    fetch(`/api/vendor/products`).then(res => res.json()).then(products => {
        const product = products.find(p => p.id === id);
        if (!product) return;
        currentEditProductId = id;
        document.getElementById('edit-product-id').value = id;
        document.getElementById('edit-product-name').value = product.product_name;
        document.getElementById('edit-product-type').value = product.product_type;
        document.getElementById('edit-product-price').value = product.selling_price;
        document.getElementById('edit-product-stock').value = product.stock ?? '';
        document.getElementById('edit-product-image-preview').src = product.image_path ? `/storage/${product.image_path}` : '';
        document.getElementById('edit-product-modal').classList.remove('hidden');
    });
}
function closeEditProductModal() {
    document.getElementById('edit-product-modal').classList.add('hidden');
    currentEditProductId = null;
}

// Edit product form
const editForm = document.getElementById('vendor-edit-product-form');
editForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!currentEditProductId) return;
    const formData = new FormData(editForm);
    const res = await fetch(`/api/vendor/products/${currentEditProductId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCSRFToken() },
        body: formData
    });
    const data = await res.json();
    if (data.success) {
        closeEditProductModal();
        loadVendorProducts();
    }
});

// Delete product
async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) return;
    await fetch(`/api/vendor/products/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': getCSRFToken() }
    });
    loadVendorProducts();
}

// Toggle product status
async function toggleProductStatus(id) {
    await fetch(`/api/vendor/products/${id}/toggle-status`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCSRFToken() }
    });
    loadVendorProducts();
}

// Initial load
window.addEventListener('DOMContentLoaded', function() {
    loadVendorProducts();
});
</script>
@endsection 