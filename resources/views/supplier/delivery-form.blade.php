@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">Create Delivery</h2>
        <form id="delivery-form" class="space-y-6">
            <input type="hidden" id="order_id" name="order_id" value="{{ $order_id ?? '' }}">
            <input type="hidden" id="supplier_id" name="supplier_id" value="{{ $supplier_id ?? '' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="distribution_center_id" class="block font-semibold mb-1">Distribution Center</label>
                    <select id="distribution_center_id" name="distribution_center_id" class="w-full border rounded px-3 py-2" required title="Select a distribution center" aria-label="Distribution Center">
                        <option value="">Select Distribution Center</option>
                    </select>
                </div>
                <div>
                    <label for="vendor_id" class="block font-semibold mb-1">Vendor ID</label>
                    <input type="number" id="vendor_id" name="vendor_id" class="w-full border rounded px-3 py-2 bg-gray-100" readonly value="{{ $vendor_id ?? '' }}" title="Vendor ID" aria-label="Vendor ID" placeholder="Vendor ID">
                </div>
            </div>
            <div class="bg-blue-50 rounded p-4 mb-4">
                <h3 class="font-semibold text-blue-800 mb-2">Vendor Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Vendor Name</label>
                        <input type="text" id="vendor_name" name="vendor_name" class="w-full border rounded px-3 py-2 bg-gray-100" readonly value="{{ $vendor_name ?? '' }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Vendor Address</label>
                        <input type="text" id="vendor_address" name="vendor_address" class="w-full border rounded px-3 py-2 bg-gray-100" readonly value="{{ $vendor_address ?? '' }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Vendor Phone</label>
                        <input type="text" id="vendor_phone" name="vendor_phone" class="w-full border rounded px-3 py-2 bg-gray-100" readonly value="{{ $vendor_phone ?? '' }}">
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="vehicle_number" class="block font-semibold mb-1">Vehicle Number</label>
                    <input type="text" id="vehicle_number" name="vehicle_number" class="w-full border rounded px-3 py-2" title="Vehicle Number" aria-label="Vehicle Number" placeholder="Enter vehicle number">
                </div>
                <div>
                    <label for="driver_select" class="block font-semibold mb-1">Driver</label>
                    <select id="driver_select" name="driver_select" class="w-full border rounded px-3 py-2" required title="Select a driver" aria-label="Driver">
                        <option value="">Select Driver</option>
                    </select>
                </div>
                <div id="driver_photo_container" class="flex flex-col items-center mb-2">
                    <img id="driver_photo" src="" alt="Driver Photo" class="w-20 h-20 rounded-full object-cover border border-gray-300 hidden" />
                    <span id="driver_photo_placeholder" class="text-gray-400">No photo</span>
                </div>
                <div>
                    <label for="driver_phone" class="block font-semibold mb-1">Driver Phone</label>
                    <input type="text" id="driver_phone" name="driver_phone" class="w-full border rounded px-3 py-2 bg-gray-100" readonly required title="Driver Phone" aria-label="Driver Phone" placeholder="Driver phone">
                </div>
                <div>
                    <label for="driver_license" class="block font-semibold mb-1">Driver License</label>
                    <input type="text" id="driver_license" name="driver_license" class="w-full border rounded px-3 py-2 bg-gray-100" readonly title="Driver License" aria-label="Driver License" placeholder="Driver license">
                </div>
                <div>
                    <label for="driver_vehicle_number" class="block font-semibold mb-1">Driver Vehicle Number</label>
                    <input type="text" id="driver_vehicle_number" name="driver_vehicle_number" class="w-full border rounded px-3 py-2 bg-gray-100" readonly title="Driver Vehicle Number" aria-label="Driver Vehicle Number" placeholder="Driver vehicle number">
                </div>
                <div>
                    <label for="driver_email" class="block font-semibold mb-1">Driver Email</label>
                    <input type="text" id="driver_email" name="driver_email" class="w-full border rounded px-3 py-2 bg-gray-100" readonly title="Driver Email" aria-label="Driver Email" placeholder="Driver email">
                </div>
                <div>
                    <label for="driver_emergency_contact" class="block font-semibold mb-1">Driver Emergency Contact</label>
                    <input type="text" id="driver_emergency_contact" name="driver_emergency_contact" class="w-full border rounded px-3 py-2 bg-gray-100" readonly title="Driver Emergency Contact" aria-label="Driver Emergency Contact" placeholder="Driver emergency contact">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="scheduled_delivery_date" class="block font-semibold mb-1">Scheduled Delivery Date</label>
                    <input type="date" id="scheduled_delivery_date" name="scheduled_delivery_date" class="w-full border rounded px-3 py-2" required title="Scheduled Delivery Date" aria-label="Scheduled Delivery Date" placeholder="Scheduled delivery date">
                </div>
                <div>
                    <label for="scheduled_delivery_time" class="block font-semibold mb-1">Scheduled Delivery Time</label>
<input type="time" id="scheduled_delivery_time" name="scheduled_delivery_time" class="w-full border rounded px-3 py-2" required title="Scheduled Delivery Time" aria-label="Scheduled Delivery Time" placeholder="Scheduled delivery time">
                </div>
            </div>
            <div>
                <label for="delivery_address" class="block font-semibold mb-1">Delivery Address</label>
<input type="text" id="delivery_address" name="delivery_address" class="w-full border rounded px-3 py-2" required title="Delivery Address" aria-label="Delivery Address" placeholder="Enter delivery address">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="recipient_name" class="block font-semibold mb-1">Recipient Name</label>
                    <input type="text" id="recipient_name" name="recipient_name" class="w-full border rounded px-3 py-2" required title="Recipient Name" aria-label="Recipient Name" placeholder="Enter recipient name">
                </div>
                <div>
                    <label for="recipient_phone" class="block font-semibold mb-1">Recipient Phone</label>
                    <input type="text" id="recipient_phone" name="recipient_phone" class="w-full border rounded px-3 py-2" required title="Recipient Phone" aria-label="Recipient Phone" placeholder="Enter recipient phone">
                </div>
                <div>
                    <label for="recipient_email" class="block font-semibold mb-1">Recipient Email</label>
                    <input type="text" id="recipient_email" name="recipient_email" class="w-full border rounded px-3 py-2" required title="Recipient Email" aria-label="Recipient Email" placeholder="Enter recipient email">
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="reset" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Reset</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">Create Delivery</button>
            </div>
            <div id="delivery-error" class="text-red-600 text-sm mt-2 hidden"></div>
            <div id="delivery-success" class="text-green-600 text-sm mt-2 hidden"></div>
        </form>
    </div>
</div>
<script>
// Autofill form fields from query parameters
function getQueryParam(name) {
    const url = new URL(window.location.href);
    return url.searchParams.get(name) || '';
}

document.addEventListener('DOMContentLoaded', function() {
    const fields = [
        'order_id',
        'distribution_center_id',
        'vendor_id',
        'vendor_name',
        'vendor_address',
        'vendor_phone',
    ];
    fields.forEach(field => {
        const value = getQueryParam(field);
        if (value) {
            const input = document.getElementById(field);
            if (input) input.value = value;
        }
    });

    // Load drivers for this supplier (use supplier_id, not vendor_id)
    const supplierId = document.getElementById('supplier_id').value;
    if (supplierId) {
        fetch(`/api/supplier/${supplierId}/drivers`)
            .then(res => res.json())
            .then(drivers => {
                const driverSelect = document.getElementById('driver_select');
                driverSelect.innerHTML = '<option value="">Select Driver</option>';
                drivers.forEach(driver => {
                    const option = document.createElement('option');
                    option.value = JSON.stringify(driver);
                    option.textContent = driver.name;
                    driverSelect.appendChild(option);
                });
            });
    }

    // Driver dropdown autofill
    const driverSelect = document.getElementById('driver_select');
    driverSelect.addEventListener('change', function() {
        if (driverSelect.value) {
            const driver = JSON.parse(driverSelect.value);
            document.getElementById('driver_phone').value = driver.phone;
            document.getElementById('driver_license').value = driver.license;
            document.getElementById('driver_vehicle_number').value = driver.vehicle_number || '';
            document.getElementById('driver_email').value = driver.email || '';
            document.getElementById('driver_emergency_contact').value = driver.emergency_contact || '';
            // Show driver photo
            const photo = driver.photo ? `/storage/${driver.photo}` : '';
            const img = document.getElementById('driver_photo');
            const placeholder = document.getElementById('driver_photo_placeholder');
            if (photo) {
                img.src = photo;
                img.style.display = 'block';
                placeholder.style.display = 'none';
            } else {
                img.src = '';
                img.style.display = 'none';
                placeholder.style.display = 'block';
            }
        } else {
            document.getElementById('driver_phone').value = '';
            document.getElementById('driver_license').value = '';
            document.getElementById('driver_vehicle_number').value = '';
            document.getElementById('driver_email').value = '';
            document.getElementById('driver_emergency_contact').value = '';
            // Hide photo
            const img = document.getElementById('driver_photo');
            const placeholder = document.getElementById('driver_photo_placeholder');
            img.src = '';
            img.style.display = 'none';
            placeholder.style.display = 'block';
        }
    });

    // Fetch and populate distribution centers
    fetch('/api/distribution-centers')
        .then(res => res.json())
        .then(centers => {
            const dcSelect = document.getElementById('distribution_center_id');
            dcSelect.innerHTML = '<option value="">Select Distribution Center</option>';
            centers.forEach(center => {
                const option = document.createElement('option');
                option.value = center.id;
                option.textContent = center.center_name + ' (' + center.center_address + ')';
                dcSelect.appendChild(option);
            });
            // If a value was passed in the query, select it
            const dcId = getQueryParam('distribution_center_id');
            if (dcId) dcSelect.value = dcId;
        });
});

// AJAX delivery creation
const deliveryForm = document.getElementById('delivery-form');
deliveryForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const driver = form.driver_select.value ? JSON.parse(form.driver_select.value) : {};
    const data = {
        order_id: form.order_id.value,
        distribution_center_id: form.distribution_center_id.value,
        vendor_id: form.vendor_id.value,
        vehicle_number: form.vehicle_number.value || driver.vehicle_number,
        driver_name: driver.name,
        driver_phone: driver.phone,
        driver_license: driver.license,
        scheduled_delivery_date: form.scheduled_delivery_date.value,
        scheduled_delivery_time: form.scheduled_delivery_time.value,
        delivery_address: form.delivery_address.value,
        recipient_name: form.recipient_name.value,
        recipient_phone: form.recipient_phone.value,
    };
    fetch('/api/deliveries', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify(data)
    })
    .then(async res => {
        let response;
        try { response = await res.json(); } catch { response = {}; }
        if (res.ok && response.success) {
            document.getElementById('delivery-success').textContent = 'Delivery created and vendor notified!';
            document.getElementById('delivery-success').classList.remove('hidden');
            document.getElementById('delivery-error').classList.add('hidden');
            form.reset();
        } else {
            let errorMsg = 'Failed to create delivery.';
            if (response && response.errors) {
                errorMsg = Object.values(response.errors).flat().join(' ');
            } else if (response && response.message) {
                errorMsg = response.message;
            }
            document.getElementById('delivery-error').textContent = errorMsg;
            document.getElementById('delivery-error').classList.remove('hidden');
            document.getElementById('delivery-success').classList.add('hidden');
        }
    })
    .catch(err => {
        document.getElementById('delivery-error').textContent = 'An error occurred.';
        document.getElementById('delivery-error').classList.remove('hidden');
        document.getElementById('delivery-success').classList.add('hidden');
    });
});
</script>
@endsection 