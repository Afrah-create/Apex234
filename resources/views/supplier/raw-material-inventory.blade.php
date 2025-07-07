@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-blue-900 mb-1">Raw Material Inventory</h1>
            <p class="text-gray-600">Monitor and manage your real-time raw material inventory. Use the search and filters to quickly find what you need.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" id="summary-cards">
        <div class="bg-blue-100 text-blue-900 rounded-lg p-6 flex flex-col items-center shadow" id="milk-card">
            <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9"/></svg>
            <div class="text-2xl font-bold" id="milk-qty">0 L</div>
            <div class="text-lg capitalize mt-1">Milk</div>
            <div class="text-xs text-gray-500 mt-1" id="milk-batches">0 batches</div>
        </div>
        <div class="bg-yellow-100 text-yellow-900 rounded-lg p-6 flex flex-col items-center shadow" id="sugar-card">
            <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 12h8" stroke="currentColor" stroke-width="2"/></svg>
            <div class="text-2xl font-bold" id="sugar-qty">0 kg</div>
            <div class="text-lg capitalize mt-1">Sugar</div>
            <div class="text-xs text-gray-500 mt-1" id="sugar-batches">0 batches</div>
        </div>
        <div class="bg-green-100 text-green-900 rounded-lg p-6 flex flex-col items-center shadow" id="fruit-card">
            <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 2v2m0 16v2m10-10h-2M4 12H2" stroke="currentColor" stroke-width="2"/></svg>
            <div class="text-2xl font-bold" id="fruit-qty">0 kg</div>
            <div class="text-lg capitalize mt-1">Fruit</div>
            <div class="text-xs text-gray-500 mt-1" id="fruit-batches">0 batches</div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
        <input type="text" id="search-input" class="w-full md:w-1/3 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Search by material name or batch code...">
        <select id="type-filter" class="w-full md:w-1/6 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">All Types</option>
            <option value="milk">Milk</option>
            <option value="sugar">Sugar</option>
            <option value="fruit">Fruit</option>
        </select>
        <select id="status-filter" class="w-full md:w-1/6 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">All Statuses</option>
            <option value="Pending">Pending</option>
            <option value="Delivered">Delivered</option>
            <option value="Expired">Expired</option>
        </select>
    </div>

    <!-- Add Raw Material Button -->
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('supplier.add-raw-material') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow font-semibold transition">Add Raw Material</a>
        <a href="{{ route('supplier.profile') }}" class="bg-gray-100 hover:bg-gray-200 text-blue-900 px-6 py-2 rounded shadow font-semibold transition ml-2">Profile & Farm Info</a>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow p-4 overflow-x-auto">
        <table class="min-w-full text-sm" id="inventory-table">
            <thead>
                <tr class="bg-blue-50">
                    <th class="px-4 py-2 text-left">Material</th>
                    <th class="px-4 py-2 text-left">Type</th>
                    <th class="px-4 py-2 text-left">Batch</th>
                    <th class="px-4 py-2 text-left">Quantity</th>
                    <th class="px-4 py-2 text-left">Unit</th>
                    <th class="px-4 py-2 text-left">Harvest Date</th>
                    <th class="px-4 py-2 text-left">Expiry Date</th>
                    <th class="px-4 py-2 text-left">Quality</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody id="inventory-body">
                <tr id="empty-row">
                    <td colspan="9" class="text-center py-12 text-gray-400">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                            <span class="text-lg font-semibold">No raw material inventory found</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Pagination -->
        <div class="flex justify-end mt-4" id="pagination-controls"></div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="modal-bg" class="fixed inset-0 bg-black bg-opacity-40 z-40 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-4 sm:p-6 relative mx-2">
            <button id="close-modal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">&times;</button>
            <h2 id="modal-title" class="text-xl font-bold mb-4">Add Raw Material</h2>
            <form id="material-form" class="space-y-3">
                <input type="hidden" id="material-id">
                <div>
                    <label class="block font-semibold mb-1">Material Name</label>
                    <input type="text" id="material_name" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block font-semibold mb-1">Type</label>
                        <select id="material_type" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select type</option>
                            <option value="milk">Milk</option>
                            <option value="sugar">Sugar</option>
                            <option value="fruit">Fruit</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block font-semibold mb-1">Batch Code</label>
                        <input type="text" id="material_code" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block font-semibold mb-1">Quantity</label>
                        <input type="number" id="quantity" class="w-full border rounded px-3 py-2" min="0.01" step="0.01" required>
                    </div>
                    <div class="flex-1">
                        <label class="block font-semibold mb-1">Unit of Measure</label>
                        <input type="text" id="unit_of_measure" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block font-semibold mb-1">Harvest Date</label>
                        <input type="date" id="harvest_date" class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="flex-1">
                        <label class="block font-semibold mb-1">Expiry Date</label>
                        <input type="date" id="expiry_date" class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block font-semibold mb-1">Quality Grade</label>
                        <input type="text" id="quality_grade" class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="flex-1">
                        <label class="block font-semibold mb-1">Status</label>
                        <select id="status" class="w-full border rounded px-3 py-2" required>
                            <option value="pending">Pending</option>
                            <option value="delivered">Delivered</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">Save</button>
                </div>
                <div id="form-error" class="text-red-600 text-sm mt-2 hidden"></div>
            </form>
        </div>
    </div>
</div>
<script>
let allInventory = [];
let filteredInventory = [];
let currentPage = 1;
const pageSize = 10;

function updateSummaryCards(data) {
    const types = ['milk', 'sugar', 'fruit'];
    const units = { milk: 'L', sugar: 'kg', fruit: 'kg' };
    types.forEach(type => {
        const items = data.filter(i => i.material_type === type);
        const total = items.reduce((sum, i) => sum + Number(i.quantity), 0);
        document.getElementById(type + '-qty').textContent = `${total} ${units[type]}`;
        document.getElementById(type + '-batches').textContent = `${items.length} batch${items.length !== 1 ? 'es' : ''}`;
    });
}

function renderTable(data) {
    const tbody = document.getElementById('inventory-body');
    const emptyRow = document.getElementById('empty-row');
    tbody.innerHTML = '';
    if (data.length === 0) {
        tbody.appendChild(emptyRow);
        emptyRow.style.display = '';
        return;
    }
    emptyRow.style.display = 'none';
    data.forEach(item => {
        tbody.innerHTML += `<tr>
            <td class='px-4 py-2'>${item.material_name}</td>
            <td class='px-4 py-2 capitalize'>${item.material_type}</td>
            <td class='px-4 py-2'>${item.material_code}</td>
            <td class='px-4 py-2'>${item.quantity}</td>
            <td class='px-4 py-2'>${item.unit_of_measure}</td>
            <td class='px-4 py-2'>${item.harvest_date ?? '-'}</td>
            <td class='px-4 py-2'>${item.expiry_date ?? '-'}</td>
            <td class='px-4 py-2'>${item.quality_grade ?? '-'}</td>
            <td class='px-4 py-2'>${renderStatusBadge(item.status)}</td>
            <td class='px-4 py-2'>
                <button class='edit-btn text-blue-600 hover:underline' data-id='${item.id}'>Edit</button>
            </td>
        </tr>`;
    });
}

function renderStatusBadge(status) {
    let color = 'bg-gray-200 text-gray-800';
    if (status === 'pending') color = 'bg-yellow-100 text-yellow-800';
    if (status === 'delivered') color = 'bg-green-100 text-green-800';
    if (status === 'expired') color = 'bg-red-100 text-red-800';
    return `<span class="px-3 py-1 rounded-full text-xs font-semibold ${color}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
}

function paginate(data, page, size) {
    const start = (page - 1) * size;
    return data.slice(start, start + size);
}

function renderPaginationControls(total, page, size) {
    const controls = document.getElementById('pagination-controls');
    const pageCount = Math.ceil(total / size);
    if (pageCount <= 1) { controls.innerHTML = ''; return; }
    let html = '';
    for (let i = 1; i <= pageCount; i++) {
        html += `<button class="mx-1 px-3 py-1 rounded ${i === page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'}" onclick="goToPage(${i})">${i}</button>`;
    }
    controls.innerHTML = html;
}

function goToPage(page) {
    currentPage = page;
    updateTable();
}

function updateTable() {
    const paginated = paginate(filteredInventory, currentPage, pageSize);
    renderTable(paginated);
    renderPaginationControls(filteredInventory.length, currentPage, pageSize);
}

function applyFilters() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const type = document.getElementById('type-filter').value;
    const status = document.getElementById('status-filter').value;
    filteredInventory = allInventory.filter(item => {
        const matchesSearch = item.material_name.toLowerCase().includes(search) || item.material_code.toLowerCase().includes(search);
        const matchesType = !type || item.material_type === type;
        const matchesStatus = !status || item.status === status;
        return matchesSearch && matchesType && matchesStatus;
    });
    currentPage = 1;
    updateTable();
}

document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/supplier/raw-material-inventory')
        .then(res => res.json())
        .then(data => {
            allInventory = data.inventory || [];
            filteredInventory = allInventory;
            updateSummaryCards(allInventory);
            updateTable();
        })
        .catch(() => {
            updateSummaryCards([]);
            document.getElementById('inventory-body').innerHTML = '';
            document.getElementById('empty-row').style.display = '';
        });
    document.getElementById('search-input').addEventListener('input', applyFilters);
    document.getElementById('type-filter').addEventListener('change', applyFilters);
    document.getElementById('status-filter').addEventListener('change', applyFilters);

    // Modal logic
    const modalBg = document.getElementById('modal-bg');
    const addBtn = document.getElementById('add-btn');
    const closeModal = document.getElementById('close-modal');
    const form = document.getElementById('material-form');
    const formError = document.getElementById('form-error');
    let isEdit = false;
    let editId = null;

    function openModal(edit = false, data = null) {
        isEdit = edit;
        editId = data ? data.id : null;
        document.getElementById('modal-title').textContent = edit ? 'Edit Raw Material' : 'Add Raw Material';
        form.reset();
        formError.classList.add('hidden');
        if (edit && data) {
            document.getElementById('material-id').value = data.id;
            document.getElementById('material_name').value = data.material_name;
            document.getElementById('material_type').value = data.material_type;
            document.getElementById('material_code').value = data.material_code;
            document.getElementById('quantity').value = data.quantity;
            document.getElementById('unit_of_measure').value = data.unit_of_measure;
            document.getElementById('harvest_date').value = data.harvest_date || '';
            document.getElementById('expiry_date').value = data.expiry_date || '';
            document.getElementById('quality_grade').value = data.quality_grade || '';
            document.getElementById('status').value = data.status;
        }
        modalBg.classList.remove('hidden');
    }
    function closeModalFunc() {
        modalBg.classList.add('hidden');
    }
    addBtn.addEventListener('click', () => openModal(false));
    closeModal.addEventListener('click', closeModalFunc);
    modalBg.addEventListener('click', function(e) { if (e.target === modalBg) closeModalFunc(); });

    // Handle form submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        formError.classList.add('hidden');
        const payload = {
            material_name: document.getElementById('material_name').value,
            material_type: document.getElementById('material_type').value,
            material_code: document.getElementById('material_code').value,
            quantity: document.getElementById('quantity').value,
            unit_of_measure: document.getElementById('unit_of_measure').value,
            harvest_date: document.getElementById('harvest_date').value,
            expiry_date: document.getElementById('expiry_date').value,
            quality_grade: document.getElementById('quality_grade').value,
            status: document.getElementById('status').value,
        };
        const url = isEdit ? `/api/supplier/raw-material-inventory/${editId}` : '/api/supplier/raw-material-inventory';
        const method = isEdit ? 'PUT' : 'POST';
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeModalFunc();
                // Refresh inventory
                return fetch('/api/supplier/raw-material-inventory')
                    .then(res => res.json())
                    .then(data => {
                        allInventory = data.inventory || [];
                        filteredInventory = allInventory;
                        updateSummaryCards(allInventory);
                        updateTable();
                    });
            } else {
                throw new Error(data.message || 'Failed to save.');
            }
        })
        .catch(err => {
            formError.textContent = err.message;
            formError.classList.remove('hidden');
        });
    });

    // Edit button logic
    document.getElementById('inventory-body').addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-btn')) {
            const id = e.target.getAttribute('data-id');
            const item = allInventory.find(i => i.id == id);
            if (item) openModal(true, item);
        }
    });
});
window.goToPage = goToPage;
</script>
@endsection 