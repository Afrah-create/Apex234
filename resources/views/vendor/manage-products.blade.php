@extends('layouts.app')

@section('content')
<main class="main-content">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 md:mb-8">Inventory Management</h1>
    
    <!-- Inventory Summary Cards (real-time) -->
    <div id="inventory-summary-cards" class="flex flex-wrap gap-4 mb-8">
        <div class="summary-card flex-1 min-w-[180px] bg-white rounded-lg shadow p-4 flex flex-col items-center">
            <div class="font-bold text-gray-700 text-sm mb-1">Total Batches</div>
            <div id="summary-total-products" class="text-2xl font-bold text-blue-600">-</div>
        </div>
        <div class="summary-card flex-1 min-w-[180px] bg-white rounded-lg shadow p-4 flex flex-col items-center">
            <div class="font-bold text-gray-700 text-sm mb-1">Total Value</div>
            <div id="summary-total-value" class="text-2xl font-bold text-green-600">-</div>
        </div>
        <div class="summary-card flex-1 min-w-[180px] bg-white rounded-lg shadow p-4 flex flex-col items-center">
            <div class="font-bold text-gray-700 text-sm mb-1">Total Products Available</div>
            <div id="summary-total-available" class="text-2xl font-bold text-indigo-600">-</div>
        </div>
        <div class="summary-card flex-1 min-w-[180px] bg-white rounded-lg shadow p-4 flex flex-col items-center">
            <div class="font-bold text-gray-700 text-sm mb-1">Total Reserved</div>
            <div id="summary-total-reserved" class="text-2xl font-bold text-yellow-600">-</div>
        </div>
        <div class="summary-card flex-1 min-w-[180px] bg-white rounded-lg shadow p-4 flex flex-col items-center">
            <div class="font-bold text-gray-700 text-sm mb-1">Total Damaged</div>
            <div id="summary-total-damaged" class="text-2xl font-bold text-red-600">-</div>
        </div>
        <div class="summary-card flex-1 min-w-[180px] bg-white rounded-lg shadow p-4 flex flex-col items-center">
            <div class="font-bold text-gray-700 text-sm mb-1">Total Expired</div>
            <div id="summary-total-expired" class="text-2xl font-bold text-gray-500">-</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6 md:mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-4 md:space-x-8 overflow-x-auto">
                <button id="products-tab" class="tab-button border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600 whitespace-nowrap">
                    Product Inventory
                </button>
                <button id="raw-materials-tab" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">
                    Raw Materials
                </button>
            </nav>
        </div>
    </div>

    <!-- Product Inventory Section -->
    <div id="products-section" class="inventory-section">
        @php
            $warning_max = session('inventory_warning_max', 4);
            $low_max = session('inventory_low_max', 14);
        @endphp
        <div>
            <h2 class="text-lg md:text-xl font-semibold mb-4">Product Inventory</h2>
            <!-- Search and Filter -->
            <div class="mb-4 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" id="product-search" placeholder="Search products..." class="w-full p-2 rounded border text-sm">
                </div>
                <div class="flex gap-2">
                    <select id="product-status-filter" class="p-2 rounded border text-sm">
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                    <select id="product-sort" class="p-2 rounded border text-sm">
                        <option value="name">Sort by Name</option>
                        <option value="quantity">Sort by Quantity</option>
                        <option value="expiry">Sort by Expiry</option>
                    </select>
                </div>
            </div>
            <!-- Desktop Table -->
            <div class="lg:block bg-white rounded-lg shadow-md p-4 overflow-x-auto">
                <table class="user-table w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-2 text-sm font-semibold">Product</th>
                            <th class="pb-2 text-sm font-semibold">Batch</th>
                            <th class="pb-2 text-sm font-semibold">Available</th>
                            <th class="pb-2 text-sm font-semibold">Reserved</th>
                            <th class="pb-2 text-sm font-semibold">Damaged</th>
                            <th class="pb-2 text-sm font-semibold">Expired</th>
                            <th class="pb-2 text-sm font-semibold">Status</th>
                            <th class="pb-2 text-sm font-semibold">Value (UGX)</th>
                            <th class="pb-2 text-sm font-semibold">Expiry</th>
                            <th class="pb-2 text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="product-inventory-list">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span id="product-start">0</span> to <span id="product-end">0</span> of <span id="product-total">0</span> entries
                </div>
                <div class="flex gap-2">
                    <button id="product-prev" class="px-3 py-1 border rounded text-sm disabled:opacity-50" disabled>Previous</button>
                    <div id="product-pages" class="flex gap-1">
                        <!-- Populated by JS -->
                    </div>
                    <button id="product-next" class="px-3 py-1 border rounded text-sm disabled:opacity-50" disabled>Next</button>
                </div>
            </div>
        </div>

        <div>
            {{-- Lower Product Inventory section removed as per request --}}
        </div>
    </div>

    <!-- Raw Materials Section -->
    <div id="raw-materials-section" class="inventory-section hidden">
        
        <div>
            <h2 class="text-lg md:text-xl font-semibold mb-4">Raw Materials Inventory</h2>
            
            <!-- Search and Filter -->
            <div class="mb-4 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" id="raw-material-search" placeholder="Search materials..." class="w-full p-2 rounded border text-sm">
                </div>
                <div class="flex gap-2">
                    <select id="raw-material-status-filter" class="p-2 rounded border text-sm">
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="in_use">In Use</option>
                        <option value="expired">Expired</option>
                        <option value="disposed">Disposed</option>
                    </select>
                    <select id="raw-material-sort" class="p-2 rounded border text-sm">
                        <option value="name">Sort by Name</option>
                        <option value="quantity">Sort by Quantity</option>
                        <option value="expiry">Sort by Expiry</option>
                    </select>
                </div>
            </div>

            <!-- Desktop Table -->
            <div class="lg:block bg-white rounded-lg shadow-md p-4 overflow-x-auto">
                <table class="user-table w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-2 text-sm font-semibold">Material</th>
                            <th class="pb-2 text-sm font-semibold">Type</th>
                            <th class="pb-2 text-sm font-semibold">Quantity</th>
                            <th class="pb-2 text-sm font-semibold">Unit</th>
                            <th class="pb-2 text-sm font-semibold">Unit Price</th>
                            <th class="pb-2 text-sm font-semibold">Total Cost</th>
                            <th class="pb-2 text-sm font-semibold">Grade</th>
                            <th class="pb-2 text-sm font-semibold">Status</th>
                            <th class="pb-2 text-sm font-semibold">Expiry</th>
                            <th class="pb-2 text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="raw-materials-list">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span id="raw-material-start">0</span> to <span id="raw-material-end">0</span> of <span id="raw-material-total">0</span> entries
                </div>
                <div class="flex gap-2">
                    <button id="raw-material-prev" class="px-3 py-1 border rounded text-sm disabled:opacity-50" disabled>Previous</button>
                    <div id="raw-material-pages" class="flex gap-1">
                        <!-- Populated by JS -->
                    </div>
                    <button id="raw-material-next" class="px-3 py-1 border rounded text-sm disabled:opacity-50" disabled>Next</button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Move all scripts to the very end of the file -->
<script>
// Helper to get CSRF token
function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

// Pagination variables
let productCurrentPage = 1;
let rawMaterialCurrentPage = 1;
const itemsPerPage = 10;
let productData = [];
let rawMaterialData = [];

// Add real-time summary card update
async function loadInventorySummaryCards() {
    try {
        const response = await fetch('/api/vendor/inventory/summary');
        const data = await response.json();
        console.log('Inventory summary data:', data);
        // Helper to safely format numbers
        function safeNumber(val) {
            if (val === null || val === undefined || isNaN(val)) return '-';
            if (typeof val === 'string' && val.trim() === '') return '-';
            return Number(val).toLocaleString();
        }
        document.getElementById('summary-total-products').textContent = safeNumber(data.product_summary?.total_batches);
        document.getElementById('summary-total-value').textContent = safeNumber(data.product_summary?.total_value) + ' UGX';
        document.getElementById('summary-total-available').textContent = safeNumber(data.product_summary?.total_available);
        document.getElementById('summary-total-reserved').textContent = safeNumber(data.product_summary?.total_reserved);
        document.getElementById('summary-total-damaged').textContent = safeNumber(data.product_summary?.total_damaged);
        document.getElementById('summary-total-expired').textContent = safeNumber(data.product_summary?.total_expired);
    } catch (error) {
        console.error('Error loading inventory summary:', error);
        [
            'summary-total-products',
            'summary-total-value',
            'summary-total-available',
            'summary-total-reserved',
            'summary-total-damaged',
            'summary-total-expired'
        ].forEach(id => {
            document.getElementById(id).textContent = '-';
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadInventorySummaryCards();
    // Tab functionality
    const productsTab = document.getElementById('products-tab');
    const rawMaterialsTab = document.getElementById('raw-materials-tab');
    const productsSection = document.getElementById('products-section');
    const rawMaterialsSection = document.getElementById('raw-materials-section');

    if (productsTab && rawMaterialsTab && productsSection && rawMaterialsSection) {
        productsTab.addEventListener('click', function() {
            productsTab.classList.add('border-blue-500', 'text-blue-600');
            productsTab.classList.remove('border-transparent', 'text-gray-500');
            rawMaterialsTab.classList.remove('border-blue-500', 'text-blue-600');
            rawMaterialsTab.classList.add('border-transparent', 'text-gray-500');
            productsSection.classList.remove('hidden');
            rawMaterialsSection.classList.add('hidden');
        });

        rawMaterialsTab.addEventListener('click', function() {
            rawMaterialsTab.classList.add('border-blue-500', 'text-blue-600');
            rawMaterialsTab.classList.remove('border-transparent', 'text-gray-500');
            productsTab.classList.remove('border-blue-500', 'text-blue-600');
            productsTab.classList.add('border-transparent', 'text-gray-500');
            rawMaterialsSection.classList.remove('hidden');
            productsSection.classList.add('hidden');
        });
    }

    const productSearch = document.getElementById('product-search');
    if (productSearch) {
        productSearch.addEventListener('input', filterProducts);
    }
    const productStatusFilter = document.getElementById('product-status-filter');
    if (productStatusFilter) {
        productStatusFilter.addEventListener('change', filterProducts);
    }
    const productSort = document.getElementById('product-sort');
    if (productSort) {
        productSort.addEventListener('change', filterProducts);
    }
    const rawMaterialSearch = document.getElementById('raw-material-search');
    if (rawMaterialSearch) {
        rawMaterialSearch.addEventListener('input', filterRawMaterials);
    }
    const rawMaterialStatusFilter = document.getElementById('raw-material-status-filter');
    if (rawMaterialStatusFilter) {
        rawMaterialStatusFilter.addEventListener('change', filterRawMaterials);
    }
    const rawMaterialSort = document.getElementById('raw-material-sort');
    if (rawMaterialSort) {
        rawMaterialSort.addEventListener('change', filterRawMaterials);
    }
    // Load initial data
    loadProductInventory();
    loadRawMaterials();
});

// Load inventory summary
async function loadInventorySummary() {
    try {
        const response = await fetch('/api/vendor/inventory/summary');
        const data = await response.json();
        
        document.getElementById('total-products').textContent = data.product_summary.total_batches || 0;
        document.getElementById('total-value').textContent = 
            ((data.product_summary.total_value || 0) + (data.raw_material_summary.total_cost || 0)).toLocaleString() + ' UGX';
        
        // Calculate low stock items
        const lowStockCount = data.product_summary.total_available <= 10 ? 1 : 0;
        document.getElementById('low-stock-items').textContent = lowStockCount;
    } catch (error) {
        console.error('Error loading inventory summary:', error);
    }
}

// Load product inventory
async function loadProductInventory() {
    try {
        const response = await fetch('/api/vendor/inventory');
        const data = await response.json();
        
        productData = data.product_inventory || [];
        renderProductInventory();
    } catch (error) {
        console.error('Error loading product inventory:', error);
    }
}

// Filter and render product inventory
function filterProducts() {
    const searchTerm = document.getElementById('product-search').value.toLowerCase();
    const statusFilter = document.getElementById('product-status-filter').value;
    const sortBy = document.getElementById('product-sort').value;
    
    let filtered = productData.filter(item => {
        const matchesSearch = item.product_name.toLowerCase().includes(searchTerm) || 
                            item.batch_number.toLowerCase().includes(searchTerm);
        const matchesStatus = !statusFilter || item.inventory_status === statusFilter;
        return matchesSearch && matchesStatus;
    });
    
    // Sort
    filtered.sort((a, b) => {
        switch(sortBy) {
            case 'quantity':
                return b.quantity_available - a.quantity_available;
            case 'expiry':
                return new Date(a.expiry_date) - new Date(b.expiry_date);
            default:
                return a.product_name.localeCompare(b.product_name);
        }
    });
    
    productData = filtered;
    productCurrentPage = 1;
    renderProductInventory();
}

// Render product inventory with pagination
function renderProductInventory() {
    const startIndex = (productCurrentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = productData.slice(startIndex, endIndex);
    
    // Desktop table
    const tbody = document.getElementById('product-inventory-list');
    tbody.innerHTML = '';
    
    paginatedData.forEach(inventory => {
        const tr = document.createElement('tr');
        tr.className = 'border-b hover:bg-gray-50';
        
        const statusClass = inventory.inventory_status === 'available' ? 'text-green-600' : 
                          inventory.inventory_status === 'low_stock' ? 'text-yellow-600' : 'text-red-600';
        
        tr.innerHTML = `
            <td class="py-2 text-sm">${inventory.product_name}</td>
            <td class="py-2 text-sm">${inventory.batch_number}</td>
            <td class="py-2 text-sm">${inventory.quantity_available}</td>
            <td class="py-2 text-sm">${inventory.quantity_reserved}</td>
            <td class="py-2 text-sm">${inventory.quantity_damaged}</td>
            <td class="py-2 text-sm">${inventory.quantity_expired}</td>
            <td class="py-2"><span class="font-bold ${statusClass} text-sm">${inventory.inventory_status.replace('_', ' ').toUpperCase()}</span></td>
            <td class="py-2 text-sm">${parseFloat(inventory.total_value).toLocaleString()} UGX</td>
            <td class="py-2 text-sm">${inventory.expiry_date}</td>
            <td class="py-2">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded mr-1 text-xs" onclick="editProductInventory(${inventory.yogurt_product_id})">Edit</button>
                <button class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs" onclick="deleteProductInventory(${inventory.id})">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    // Update pagination info
    document.getElementById('product-start').textContent = startIndex + 1;
    document.getElementById('product-end').textContent = Math.min(endIndex, productData.length);
    document.getElementById('product-total').textContent = productData.length;
    
    // Update pagination buttons
    updateProductPagination();
}

// Update product pagination
function updateProductPagination() {
    const totalPages = Math.ceil(productData.length / itemsPerPage);
    const pagesContainer = document.getElementById('product-pages');
    const prevBtn = document.getElementById('product-prev');
    const nextBtn = document.getElementById('product-next');
    
    pagesContainer.innerHTML = '';
    
    for (let i = 1; i <= totalPages; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.className = `px-3 py-1 border rounded text-sm ${i === productCurrentPage ? 'bg-blue-600 text-white' : ''}`;
        pageBtn.textContent = i;
        pageBtn.onclick = () => {
            productCurrentPage = i;
            renderProductInventory();
        };
        pagesContainer.appendChild(pageBtn);
    }
    
    prevBtn.disabled = productCurrentPage === 1;
    nextBtn.disabled = productCurrentPage === totalPages;
    
    prevBtn.onclick = () => {
        if (productCurrentPage > 1) {
            productCurrentPage--;
            renderProductInventory();
        }
    };
    
    nextBtn.onclick = () => {
        if (productCurrentPage < totalPages) {
            productCurrentPage++;
            renderProductInventory();
        }
    };
}

// Load raw materials
async function loadRawMaterials() {
    try {
        const response = await fetch('/api/vendor/inventory');
        const data = await response.json();
        
        rawMaterialData = data.raw_materials || [];
        renderRawMaterials();
    } catch (error) {
        console.error('Error loading raw materials:', error);
    }
}

// Filter and render raw materials
function filterRawMaterials() {
    const searchTerm = document.getElementById('raw-material-search').value.toLowerCase();
    const statusFilter = document.getElementById('raw-material-status-filter').value;
    const sortBy = document.getElementById('raw-material-sort').value;
    
    let filtered = rawMaterialData.filter(item => {
        const matchesSearch = item.material_name.toLowerCase().includes(searchTerm) || 
                            item.material_type.toLowerCase().includes(searchTerm);
        const matchesStatus = !statusFilter || item.status === statusFilter;
        return matchesSearch && matchesStatus;
    });
    
    // Sort
    filtered.sort((a, b) => {
        switch(sortBy) {
            case 'quantity':
                return b.quantity - a.quantity;
            case 'expiry':
                return new Date(a.expiry_date) - new Date(b.expiry_date);
            default:
                return a.material_name.localeCompare(b.material_name);
        }
    });
    
    rawMaterialData = filtered;
    rawMaterialCurrentPage = 1;
    renderRawMaterials();
}

// Render raw materials with pagination
function renderRawMaterials() {
    const startIndex = (rawMaterialCurrentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = rawMaterialData.slice(startIndex, endIndex);
    
    // Desktop table
    const tbody = document.getElementById('raw-materials-list');
    tbody.innerHTML = '';
    
    paginatedData.forEach(material => {
        const tr = document.createElement('tr');
        tr.className = 'border-b hover:bg-gray-50';
        
        const statusClass = material.status === 'available' ? 'text-green-600' : 
                          material.status === 'in_use' ? 'text-blue-600' : 
                          material.status === 'expired' ? 'text-red-600' : 'text-gray-600';
        
        tr.innerHTML = `
            <td class="py-2 text-sm">${material.material_name}</td>
            <td class="py-2 text-sm">${material.material_type}</td>
            <td class="py-2 text-sm">${material.quantity}</td>
            <td class="py-2 text-sm">${material.unit_of_measure}</td>
            <td class="py-2 text-sm">${parseFloat(material.unit_price).toLocaleString()} UGX</td>
            <td class="py-2 text-sm">${parseFloat(material.total_cost).toLocaleString()} UGX</td>
            <td class="py-2 text-sm">${material.quality_grade}</td>
            <td class="py-2"><span class="font-bold ${statusClass} text-sm">${material.status.toUpperCase()}</span></td>
            <td class="py-2 text-sm">${material.expiry_date}</td>
            <td class="py-2">
                <button class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded mr-1 text-xs" onclick="editRawMaterial(${material.id})">Edit</button>
                <button class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs" onclick="deleteRawMaterial(${material.id})">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    // Update pagination info
    document.getElementById('raw-material-start').textContent = startIndex + 1;
    document.getElementById('raw-material-end').textContent = Math.min(endIndex, rawMaterialData.length);
    document.getElementById('raw-material-total').textContent = rawMaterialData.length;
    
    // Update pagination buttons
    updateRawMaterialPagination();
}

// Update raw material pagination
function updateRawMaterialPagination() {
    const totalPages = Math.ceil(rawMaterialData.length / itemsPerPage);
    const pagesContainer = document.getElementById('raw-material-pages');
    const prevBtn = document.getElementById('raw-material-prev');
    const nextBtn = document.getElementById('raw-material-next');
    
    pagesContainer.innerHTML = '';
    
    for (let i = 1; i <= totalPages; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.className = `px-3 py-1 border rounded text-sm ${i === rawMaterialCurrentPage ? 'bg-green-600 text-white' : ''}`;
        pageBtn.textContent = i;
        pageBtn.onclick = () => {
            rawMaterialCurrentPage = i;
            renderRawMaterials();
        };
        pagesContainer.appendChild(pageBtn);
    }
    
    prevBtn.disabled = rawMaterialCurrentPage === 1;
    nextBtn.disabled = rawMaterialCurrentPage === totalPages;
    
    prevBtn.onclick = () => {
        if (rawMaterialCurrentPage > 1) {
            rawMaterialCurrentPage--;
            renderRawMaterials();
        }
    };
    
    nextBtn.onclick = () => {
        if (rawMaterialCurrentPage < totalPages) {
            rawMaterialCurrentPage++;
            renderRawMaterials();
        }
    };
}

// Add product inventory form
const addProductInventoryForm = document.getElementById('add-product-inventory-form');
addProductInventoryForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(addProductInventoryForm);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch('/api/vendor/inventory/products', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('add-product-inventory-success').classList.remove('hidden');
            addProductInventoryForm.reset();
            loadProductInventory();
            loadInventorySummary();
            setTimeout(() => document.getElementById('add-product-inventory-success').classList.add('hidden'), 3000);
        } else {
            alert('Error adding product inventory: ' + (result.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding product inventory');
    }
});

// Add raw material form
const addRawMaterialForm = document.getElementById('add-raw-material-form');
addRawMaterialForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(addRawMaterialForm);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch('/api/vendor/inventory/raw-materials', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('add-raw-material-success').classList.remove('hidden');
            addRawMaterialForm.reset();
            loadRawMaterials();
            loadInventorySummary();
            setTimeout(() => document.getElementById('add-raw-material-success').classList.add('hidden'), 3000);
        } else {
            alert('Error adding raw material: ' + (result.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding raw material');
    }
});

// Edit product inventory - redirect to edit view
function editProductInventory(id) {
    window.location.href = `/vendor/products/${id}/edit`;
}

// Delete product inventory
async function deleteProductInventory(id) {
    (async function() {
        const confirmed = await showConfirmModal('Are you sure you want to delete this product inventory?', 'Delete Product Inventory');
        if (!confirmed) return;
        try {
            const response = await fetch(`/api/vendor/inventory/products/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken()
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                loadProductInventory();
                loadInventorySummary();
            } else {
                alert('Error deleting product inventory: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error deleting product inventory');
        }
    })();
}

// Edit raw material - redirect to edit view
function editRawMaterial(id) {
    window.location.href = `/vendor/raw-materials/${id}/edit`;
}

// Delete raw material
async function deleteRawMaterial(id) {
    (async function() {
        const confirmed = await showConfirmModal('Are you sure you want to delete this raw material?', 'Delete Raw Material');
        if (!confirmed) return;
        try {
            const response = await fetch(`/api/vendor/inventory/raw-materials/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken()
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                loadRawMaterials();
                loadInventorySummary();
            } else {
                alert('Error deleting raw material: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error deleting raw material');
        }
    })();
}

// Auto-refresh data every 30 seconds for real-time updates
setInterval(() => {
    loadInventorySummary();
    loadProductInventory();
    loadRawMaterials();
}, 30000);

// Auto-calculate total value for product inventory
function calculateProductTotalValue() {
    const quantity = parseInt(document.getElementById('quantity-available').value) || 0;
    const unitCost = parseFloat(document.getElementById('unit-cost').value) || 0;
    const totalValue = quantity * unitCost;
    document.getElementById('total-value').value = totalValue.toFixed(2);
}

// Auto-calculate total cost for raw materials
function calculateRawMaterialTotalCost() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitPrice = parseFloat(document.getElementById('unit-price').value) || 0;
    const totalCost = quantity * unitPrice;
    document.getElementById('total-cost').value = totalCost.toFixed(2);
}

// Add event listeners for auto-calculation
document.addEventListener('DOMContentLoaded', function() {
    // Product inventory auto-calculation
    const quantityAvailable = document.getElementById('quantity-available');
    const unitCost = document.getElementById('unit-cost');
    
    if (quantityAvailable && unitCost) {
        quantityAvailable.addEventListener('input', calculateProductTotalValue);
        unitCost.addEventListener('input', calculateProductTotalValue);
    }
    
    // Raw material auto-calculation
    const rawQuantity = document.getElementById('quantity');
    const rawUnitPrice = document.getElementById('unit-price');
    
    if (rawQuantity && rawUnitPrice) {
        rawQuantity.addEventListener('input', calculateRawMaterialTotalCost);
        rawUnitPrice.addEventListener('input', calculateRawMaterialTotalCost);
    }
});

// Populate dairy farm select for raw material form
function populateDairyFarms() {
    fetch('/api/vendor/inventory/dairy-farms')
        .then(response => response.json())
        .then(farms => {
            const select = document.getElementById('dairy-farm-id');
            if (!select) return;
            select.innerHTML = '<option value="">Select Dairy Farm</option>';
            farms.forEach(farm => {
                const option = document.createElement('option');
                option.value = farm.id;
                option.textContent = farm.farm_name;
                select.appendChild(option);
            });
        });
}
document.addEventListener('DOMContentLoaded', populateDairyFarms);
</script>
@endsection