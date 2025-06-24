<x-app-layout>
    <div class="max-w-6xl mx-auto py-8 px-2 sm:px-4">
        <h2 class="text-2xl font-bold mb-6">Manage Users</h2>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <form method="GET" action="" class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600 hover:underline">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.users.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 whitespace-nowrap">Add New User</a>
        </div>
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 mb-0 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Roles</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 @if($loop->even) bg-gray-50 @endif">
                            <td class="px-4 py-2 whitespace-nowrap">{{ $user->id }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $user->roles->pluck('name')->join(', ') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-block bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 font-semibold mr-2">Edit</a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-block bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 font-semibold" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex justify-center mt-6">
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>
</x-app-layout> 