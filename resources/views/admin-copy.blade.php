<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Administrator Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">User Management</h3>
                    <a href="{{ route('admin.users.index') }}" class="inline-block mb-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Manage Users</a>

                    <h3 class="text-lg font-bold mt-8 mb-4">Supplier Management</h3>
                    <table class="min-w-full divide-y divide-gray-200 mb-8">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Contact</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- @foreach(App\Models\Supplier::all() as $supplier) -->
                            <tr class="border-b">
                                <td class="px-4 py-2">1</td>
                                <td class="px-4 py-2">Sample Supplier</td>
                                <td class="px-4 py-2">sample@supplier.com</td>
                                <td class="px-4 py-2">
                                    <a href="#" class="text-blue-600 hover:underline">Edit</a> |
                                    <a href="#" class="text-red-600 hover:underline">Delete</a>
                                </td>
                            </tr>
                            <!-- @endforeach -->
                        </tbody>
                    </table>

                    <h3 class="text-lg font-bold mt-8 mb-4">Retailer Management</h3>
                    <table class="min-w-full divide-y divide-gray-200 mb-8">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Contact</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="px-4 py-2">1</td>
                                <td class="px-4 py-2">Sample Retailer</td>
                                <td class="px-4 py-2">sample@retailer.com</td>
                                <td class="px-4 py-2">
                                    <a href="#" class="text-blue-600 hover:underline">Edit</a> |
                                    <a href="#" class="text-red-600 hover:underline">Delete</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 class="text-lg font-bold mt-8 mb-4">Vendor Management</h3>
                    <table class="min-w-full divide-y divide-gray-200 mb-8">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Contact</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="px-4 py-2">1</td>
                                <td class="px-4 py-2">Sample Vendor</td>
                                <td class="px-4 py-2">sample@vendor.com</td>
                                <td class="px-4 py-2">
                                    <a href="#" class="text-blue-600 hover:underline">Edit</a> |
                                    <a href="#" class="text-red-600 hover:underline">Delete</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 class="text-lg font-bold mt-8 mb-4">Order Management</h3>
                    <table class="min-w-full divide-y divide-gray-200 mb-8">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Order ID</th>
                                <th class="px-4 py-2">Customer</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="px-4 py-2">1001</td>
                                <td class="px-4 py-2">Retailer A</td>
                                <td class="px-4 py-2">Pending</td>
                                <td class="px-4 py-2">
                                    <a href="#" class="text-blue-600 hover:underline">View</a> |
                                    <a href="#" class="text-red-600 hover:underline">Cancel</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 class="text-lg font-bold mt-8 mb-4">Inventory Management</h3>
                    <table class="min-w-full divide-y divide-gray-200 mb-8">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Item</th>
                                <th class="px-4 py-2">Stock</th>
                                <th class="px-4 py-2">Location</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="px-4 py-2">Yogurt</td>
                                <td class="px-4 py-2">500</td>
                                <td class="px-4 py-2">Warehouse 1</td>
                                <td class="px-4 py-2">
                                    <a href="#" class="text-blue-600 hover:underline">Edit</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 class="text-lg font-bold mt-8 mb-4">Delivery Management</h3>
                    <table class="min-w-full divide-y divide-gray-200 mb-8">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Delivery ID</th>
                                <th class="px-4 py-2">Order</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="px-4 py-2">D-001</td>
                                <td class="px-4 py-2">1001</td>
                                <td class="px-4 py-2">In Transit</td>
                                <td class="px-4 py-2">
                                    <a href="#" class="text-blue-600 hover:underline">Track</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 class="text-lg font-bold mt-8 mb-4">Quality Checks</h3>
                    <table class="min-w-full divide-y divide-gray-200 mb-8">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Check ID</th>
                                <th class="px-4 py-2">Item</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="px-4 py-2">QC-01</td>
                                <td class="px-4 py-2">Yogurt</td>
                                <td class="px-4 py-2">Passed</td>
                                <td class="px-4 py-2">
                                    <a href="#" class="text-blue-600 hover:underline">View</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 class="text-lg font-bold mt-8 mb-4">System Logs & Reports</h3>
                    <div class="bg-gray-100 p-4 rounded mb-8">
                        <p>Access and review system logs and generate reports here. (Feature coming soon)</p>
                    </div>

                    <h3 class="text-lg font-bold mb-4">Roles & Permissions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-semibold mb-2">Roles</h4>
                            <ul class="list-disc ml-6">
                                @foreach(App\Models\Role::all() as $role)
                                    <li>{{ $role->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">Permissions</h4>
                            <ul class="list-disc ml-6">
                                @foreach(App\Models\Permission::all() as $permission)
                                    <li>{{ $permission->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-bold mb-4">Admin Controls</h3>
                        <ul class="list-disc ml-6">
                            <li>Edit or delete any user</li>
                            <li>Assign or remove roles and permissions</li>
                            <li>View all supply chain data</li>
                            <li>Manage suppliers, retailers, vendors</li>
                            <li>Oversee orders, inventory, deliveries, and quality checks</li>
                            <li>Access all system logs and reports</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 