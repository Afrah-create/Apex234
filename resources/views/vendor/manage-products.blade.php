@extends('layouts.app')

@section('content')
<main class="main-content">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 md:mb-8">Inventory Management</h1>
    
    <!-- Inventory Summary Cards -->
    <div class="summary-cards mb-8">
        <div class="summary-card" style="--summary-card-border: #22c55e;">
            <div class="icon" style="background: #bbf7d0; color: #22c55e;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
        </div>
            <div class="details">
                <p>Total Products</p>
                <p id="total-products">-</p>
        </div>
        </div>
        <div class="summary-card" style="--summary-card-border: #f59e0b;">
            <div class="icon" style="background: #fef3c7; color: #f59e0b;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <div class="details">
                <p>Total Value</p>
                <p id="total-value">-</p>
            </div>
        </div>
        <div class="summary-card" style="--summary-card-border: #ef4444;">
            <div class="icon" style="background: #fee2e2; color: #ef4444;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="details">
                <p>Low Stock Items</p>
                <p id="low-stock-items">-</p>
            </div>
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
        <div class="mb-6 md:mb-8">
            <h2 class="text-lg md:text-xl font-semibold mb-4">Add New Product Inventory</h2>
            <form id="add-product-inventory-form" class="bg-white rounded-lg shadow-md p-4 md:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="product-name" class="block font-bold mb-1 text-sm">Product</label>
                        <select id="product-name" name="product_name" class="w-full p-2 rounded border text-sm" required>
                            <option value="">Select Product</option>
                            <option value="Greek Vanilla Yoghurt">Greek Vanilla Yoghurt</option>
                            <option value="Low Fat Blueberry Yoghurt">Low Fat Blueberry Yoghurt</option>
                            <option value="Organic Strawberry Yoghurt">Organic Strawberry Yoghurt</option>
                        </select>
                    </div>
                    <div>
                        <label for="batch-number" class="block font-bold mb-1 text-sm">Batch Number</label>
                        <input type="text" id="batch-number" name="batch_number" class="w-full p-2 rounded border text-sm" required>
                    </div>
                    <div>
                        <label for="quantity-available" class="block font-bold mb-1 text-sm">Quantity </label>
                        <input type="number" id="quantity-available" name="quantity_available" min="0" class="w-full p-2 rounded border text-sm" required>
                    </div>
                    <div>
                        <label for="unit-cost" class="block font-bold mb-1 text-sm">Unit Cost (UGX)</label>
                        <input type="number" id="unit-cost" name="unit_cost" min="0" step="0.01" class="w-full p-2 rounded border text-sm" required>
                    </div>
                    <div>
                        <label for="total-value" class="block font-bold mb-1 text-sm">Total Value (UGX)</label>
                        <input type="number" id="total-value" name="total_value" class="w-full p-2 rounded border text-sm bg-gray-100" readonly>
                        <p class="text-xs text-gray-500 mt-1">Auto-calculated: Unit Cost × Quantity</p>
                    </div>
                    <div>
                        <label for="production-date" class="block font-bold mb-1 text-sm">Production Date</label>
                        <input type="date" id="production-date" name="production_date" class="w-full p-2 rounded border text-sm" required>
                    </div>
                    <div>
                        <label for="expiry-date" class="block font-bold mb-1 text-sm">Expiry Date</label>
                        <input type="date" id="expiry-date" name="expiry_date" class="w-full p-2 rounded border text-sm" required>
                    </div>
                    <div>
                        <label for="storage-temperature" class="block font-bold mb-1 text-sm">Storage Temp (°C)</label>
                        <input type="number" id="storage-temperature" name="storage_temperature" min="-10" max="20" step="0.1" value="4.0" class="w-full p-2 rounded border text-sm" required>
                    </div>
                    <div>
                        <label for="storage-location" class="block font-bold mb-1 text-sm">Storage Location</label>
                        <select id="storage-location" name="storage_location" class="w-full p-2 rounded border text-sm" required>
                            <option value="refrigerator">Refrigerator</option>
                            <option value="cold_room">Cold Room</option>
                            <option value="freezer">Freezer</option>
                            <option value="warehouse">Warehouse</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="product-notes" class="block font-bold mb-1 text-sm">Notes</label>
                    <textarea id="product-notes" name="notes" class="w-full p-2 rounded border text-sm" rows="2"></textarea>
                </div>
                <div class="mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg w-full text-sm md:text-base">Add Product Inventory</button>
                </div>
            </form>
            <div id="add-product-inventory-success" class="mt-2 text-green-600 font-bold hidden text-sm">Product inventory added successfully!</div>
        </div>


        <!-- Product Inventory Statistics Table -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-8">
            <h2 class="text-lg md:text-xl font-semibold mb-4">Product Inventory Statistics</h2>
            <table class="min-w-full mb-4 border border-gray-400">
                <thead>
                    <tr class="border-b border-gray-400">
                        <th class="px-4 py-2 text-left border-r border-gray-300">Product Name</th>
                        <th class="px-4 py-2 text-left border-r border-gray-300">Available Quantity</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $products = \App\Models\YogurtProduct::all();
                        $warning_max = session('inventory_warning_max', 4);
                        $low_max = session('inventory_low_max', 14);
                        $productQuantities = \App\Models\Inventory::select('yogurt_product_id', \DB::raw('SUM(quantity_available) as total_quantity'))
                            ->groupBy('yogurt_product_id')->pluck('total_quantity', 'yogurt_product_id');
                        $confirmedOrderQuantities = \App\Models\OrderItem::whereHas('order', function($q) {
                            $q->where('order_status', 'confirmed');
                        })->select('yogurt_product_id', \DB::raw('SUM(quantity) as total_confirmed'))
                            ->groupBy('yogurt_product_id')->pluck('total_confirmed', 'yogurt_product_id');
                    @endphp
                    @forelse($products as $product)
                        @php
                            $added = $productQuantities[$product->id] ?? 0;
                            $confirmed = $confirmedOrderQuantities[$product->id] ?? 0;
                            $quantity = $added - $confirmed;
                            if ($quantity < 0) $quantity = 0;
                            $status = 'Available';
                            if ($quantity === 0) {
                                $status = 'Out of Stock';
                            } elseif ($quantity <= $warning_max) {
                                $status = 'Warning';
                            } elseif ($quantity <= $low_max) {
                                $status = 'Low';
                            }
                        @endphp
                        <tr class="border-b border-gray-300">
                            <td class="px-4 py-2 border-r border-gray-200">{{ $product->product_name }}</td>
                            <td class="px-4 py-2 border-r border-gray-200">{{ $quantity }}</td>
                            <td class="px-4 py-2">@if($status === 'Out of Stock')<span class="text-red-600 font-semibold">{{ $status }}</span>@elseif($status === 'Warning')<span class="text-yellow-600 font-semibold">{{ $status }}</span>@elseif($status === 'Low')<span class="text-orange-600 font-semibold">{{ $status }}</span>@else<span class="text-green-600 font-semibold">{{ $status }}</span>@endif</td>

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
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-center text-gray-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Status Key and Range Adjustment Form -->
            <div class="mb-2">
                <h3 class="font-semibold mb-1">Status Key:</h3>
                <ul class="text-sm mb-2">
                    <li><span class="text-red-600 font-bold">Out of Stock</span>: 0</li>
                    <li><span class="text-yellow-600 font-bold">Warning</span>: 1 - {{ $warning_max }}</li>
                    <li><span class="text-orange-600 font-bold">Low</span>: {{ $warning_max+1 }} - {{ $low_max }}</li>
                    <li><span class="text-green-600 font-bold">Available</span>: {{ $low_max+1 }} and above</li>
                </ul>
                <form method="POST" action="{{ route('vendor.inventory-status-ranges') }}" class="flex flex-col md:flex-row gap-2 items-center">
                    @csrf
                    <label class="text-sm">Warning max:
                        <input type="number" name="warning_max" value="{{ $warning_max }}" min="1" max="{{ $low_max }}" class="border rounded p-1 w-16 ml-1">
                    </label>
                    <label class="text-sm">Low max:
                        <input type="number" name="low_max" value="{{ $low_max }}" min="{{ $warning_max+1 }}" class="border rounded p-1 w-16 ml-1">
                    </label>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Save Ranges</button>
                </form>
                @if(session('range_success'))
                    <div class="text-green-600 text-sm mt-1">{{ session('range_success') }}</div>
                @endif
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

            <!-- Mobile Cards -->
            <div class="lg:hidden space-y-4" id="raw-materials-cards">
                <!-- Populated by JS -->
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

// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const productsTab = document.getElementById('products-tab');
    const rawMaterialsTab = document.getElementById('raw-materials-tab');
    const productsSection = document.getElementById('products-section');
    const rawMaterialsSection = document.getElementById('raw-materials-section');

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

    // Search and filter event listeners
    document.getElementById('product-search').addEventListener('input', filterProducts);
    document.getElementById('product-status-filter').addEventListener('change', filterProducts);
    document.getElementById('product-sort').addEventListener('change', filterProducts);
    
    document.getElementById('raw-material-search').addEventListener('input', filterRawMaterials);
    document.getElementById('raw-material-status-filter').addEventListener('change', filterRawMaterials);
    document.getElementById('raw-material-sort').addEventListener('change', filterRawMaterials);

    // Load initial data
    loadInventorySummary();
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
    
    // Mobile cards
    const cardsContainer = document.getElementById('product-inventory-cards');
    cardsContainer.innerHTML = '';
    
    paginatedData.forEach(inventory => {
        const statusClass = inventory.inventory_status === 'available' ? 'text-green-600' : 
                          inventory.inventory_status === 'low_stock' ? 'text-yellow-600' : 'text-red-600';
        
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-sm">${inventory.product_name}</h3>
                <span class="font-bold ${statusClass} text-xs">${inventory.inventory_status.replace('_', ' ').toUpperCase()}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                <div><span class="font-semibold">Batch:</span> ${inventory.batch_number}</div>
                <div><span class="font-semibold">Available:</span> ${inventory.quantity_available}</div>
                <div><span class="font-semibold">Reserved:</span> ${inventory.quantity_reserved}</div>
                <div class="text-[10px]"><span class="font-semibold">Value:</span> ${parseFloat(inventory.total_value).toLocaleString()} UGX</div>
                <div><span class="font-semibold">Expiry:</span> ${inventory.expiry_date}</div>
            </div>
            <div class="flex gap-2">
                <button class="action-btn edit-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs flex-1" onclick="editProductInventory(${inventory.yogurt_product_id})">Edit</button>
                <button class="action-btn delete-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs flex-1" onclick="deleteProductInventory(${inventory.id})">Delete</button>
            </div>
        `;
        cardsContainer.appendChild(card);
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
    
    // Mobile cards
    const cardsContainer = document.getElementById('raw-materials-cards');
    cardsContainer.innerHTML = '';
    
    paginatedData.forEach(material => {
        const statusClass = material.status === 'available' ? 'text-green-600' : 
                          material.status === 'in_use' ? 'text-blue-600' : 
                          material.status === 'expired' ? 'text-red-600' : 'text-gray-600';
        
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-sm">${material.material_name}</h3>
                <span class="font-bold ${statusClass} text-xs">${material.status.toUpperCase()}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                <div><span class="font-semibold">Type:</span> ${material.material_type}</div>
                <div><span class="font-semibold">Quantity:</span> ${material.quantity} ${material.unit_of_measure}</div>
                <div class="text-[10px]"><span class="font-semibold">Unit Price:</span> ${parseFloat(material.unit_price).toLocaleString()} UGX</div>
                <div class="text-[10px]"><span class="font-semibold">Total Cost:</span> ${parseFloat(material.total_cost).toLocaleString()} UGX</div>
                <div><span class="font-semibold">Grade:</span> ${material.quality_grade}</div>
                <div><span class="font-semibold">Expiry:</span> ${material.expiry_date}</div>
            </div>
            <div class="flex gap-2">
                <button class="action-btn edit-btn bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs flex-1" onclick="editRawMaterial(${material.id})">Edit</button>
                <button class="action-btn delete-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs flex-1" onclick="deleteRawMaterial(${material.id})">Delete</button>
            </div>
        `;
        cardsContainer.appendChild(card);
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