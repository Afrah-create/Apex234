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
                    <label class="block font-semibold mb-1">Distribution Center ID</label>
                    <input type="number" id="distribution_center_id" name="distribution_center_id" class="w-full border rounded px-3 py-2" required value="{{ $distribution_center_id ?? '' }}">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Vendor ID</label>
                    <input type="number" id="vendor_id" name="vendor_id" class="w-full border rounded px-3 py-2 bg-gray-100" readonly value="{{ $vendor_id ?? '' }}">
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
                    <label class="block font-semibold mb-1">Vehicle Number</label>
                    <input type="text" id="vehicle_number" name="vehicle_number" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Driver</label>
                    <select id="driver_select" name="driver_select" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select Driver</option>
                    </select>
                </div>
                <div id="driver_photo_container" class="flex flex-col items-center mb-2">
                    <img id="driver_photo" src="" alt="Driver Photo" class="w-20 h-20 rounded-full object-cover border border-gray-300" style="display:none;" />
                    <span id="driver_photo_placeholder" class="text-gray-400">No photo</span>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Driver Phone</label>
                    <input type="text" id="driver_phone" name="driver_phone" class="w-full border rounded px-3 py-2 bg-gray-100" readonly required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Driver License</label>
                    <input type="text" id="driver_license" name="driver_license" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Vehicle Number</label>
                    <input type="text" id="driver_vehicle_number" name="driver_vehicle_number" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Driver Email</label>
                    <input type="text" id="driver_email" name="driver_email" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Emergency Contact</label>
                    <input type="text" id="driver_emergency_contact" name="driver_emergency_contact" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Scheduled Delivery Date</label>
                    <input type="date" id="scheduled_delivery_date" name="scheduled_delivery_date" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Scheduled Delivery Time</label>
                    <input type="time" id="scheduled_delivery_time" name="scheduled_delivery_time" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>
            <div>
                <label class="block font-semibold mb-1">Delivery Address</label>
                <input type="text" id="delivery_address" name="delivery_address" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Recipient Name</label>
                    <input type="text" id="recipient_name" name="recipient_name" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Recipient Phone</label>
                    <input type="text" id="recipient_phone" name="recipient_phone" class="w-full border rounded px-3 py-2" required>
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
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            document.getElementById('delivery-success').textContent = 'Delivery created and vendor notified!';
            document.getElementById('delivery-success').classList.remove('hidden');
            form.reset();
        } else {
            document.getElementById('delivery-error').textContent = 'Failed to create delivery.';
            document.getElementById('delivery-error').classList.remove('hidden');
        }
    })
    .catch(() => {
        document.getElementById('delivery-error').textContent = 'An error occurred.';
        document.getElementById('delivery-error').classList.remove('hidden');
    });
});
</script>
@endsection 