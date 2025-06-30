@extends('layouts.app')

@section('content')
    <main class="main-content">
        <h1>Supplier</h1>
        <p>Welcome, Supplier! Use the sidebar to manage your supplies and deliveries.</p>
    </main>
    <div class="mt-6">
        <h2>Milk Batch Management</h2>
        <form id="milk-batch-form" class="bg-white rounded shadow p-4 mb-4" method="POST">
            @csrf
            <div class="mb-3">
                <label for="batch_id" class="block font-bold mb-1">Batch ID</label>
                <input type="text" id="batch_id" name="batch_id" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="block font-bold mb-1">Quantity (Liters)</label>
                <input type="number" id="quantity" name="quantity" class="w-full p-2 border rounded" min="0" step="0.01" required>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Add Batch</button>
            <div id="milk-batch-success" class="mt-2 text-green-600 font-bold hidden text-sm">Batch added successfully!</div>
            <div id="milk-batch-error" class="mt-2 text-red-600 font-bold hidden text-sm"></div>
        </form>
        <h3 class="mt-6 mb-2 font-bold">Supply History</h3>
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2">Batch ID</th>
                    <th class="px-4 py-2">Volume (L)</th>
                    <th class="px-4 py-2">Delivery Status</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody id="supply-history-body">
                <!-- Supply history rows will be inserted here by JS -->
            </tbody>
        </table>
    </div>
    <script>
    document.getElementById('milk-batch-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const batchId = form.batch_id.value;
        const quantity = form.quantity.value;
        const token = document.querySelector('input[name="_token"]').value;
        try {
            const res = await fetch('/supplier/milk-batch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ batch_id: batchId, quantity })
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('milk-batch-success').classList.remove('hidden');
                document.getElementById('milk-batch-error').classList.add('hidden');
                form.reset();
                loadSupplyHistory();
            } else {
                throw new Error(data.message || 'Submission failed');
            }
        } catch (error) {
            document.getElementById('milk-batch-error').textContent = error.message || 'An error occurred.';
            document.getElementById('milk-batch-error').classList.remove('hidden');
            document.getElementById('milk-batch-success').classList.add('hidden');
        }
    });
    async function loadSupplyHistory() {
        const res = await fetch('/supplier/milk-batch/history', { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        const tbody = document.getElementById('supply-history-body');
        tbody.innerHTML = '';
        if (data.history && data.history.length > 0) {
            data.history.forEach(row => {
                tbody.innerHTML += `<tr>
                    <td class='px-4 py-2'>${row.batch_id}</td>
                    <td class='px-4 py-2'>${row.quantity}</td>
                    <td class='px-4 py-2'><span class="status-label">${row.delivery_status}</span></td>
                    <td class='px-4 py-2'>
                        <button class="toggle-status-btn bg-gray-200 px-2 py-1 rounded" data-id="${row.id}" data-status="${row.delivery_status}">
                            ${row.delivery_status === 'Pending' ? 'Mark Delivered' : 'Mark Pending'}
                        </button>
                    </td>
                </tr>`;
            });
        } else {
            tbody.innerHTML = `<tr><td colspan='4' class='text-center py-2'>No supply history found.</td></tr>`;
        }
        attachToggleStatusEvents();
    }
    function attachToggleStatusEvents() {
        document.querySelectorAll('.toggle-status-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.getAttribute('data-id');
                const currentStatus = this.getAttribute('data-status');
                const newStatus = currentStatus === 'Pending' ? 'Delivered' : 'Pending';
                const token = document.querySelector('input[name="_token"]').value;
                try {
                    const res = await fetch(`/supplier/milk-batch/${id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: newStatus })
                    });
                    const data = await res.json();
                    if (data.success) {
                        loadSupplyHistory();
                    } else {
                        alert(data.message || 'Status update failed');
                    }
                } catch (error) {
                    alert(error.message || 'An error occurred.');
                }
            });
        });
    }
    document.addEventListener('DOMContentLoaded', loadSupplyHistory);
    </script>
@endsection 