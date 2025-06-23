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
                    <table class="min-w-full divide-y divide-gray-200 mb-8">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Roles</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(App\Models\User::all() as $user)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $user->id }}</td>
                                    <td class="px-4 py-2">{{ $user->name }}</td>
                                    <td class="px-4 py-2">{{ $user->email }}</td>
                                    <td class="px-4 py-2">
                                        {{ $user->roles->pluck('name')->join(', ') }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="#" class="text-blue-600 hover:underline">Edit</a> |
                                        <a href="#" class="text-red-600 hover:underline">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

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