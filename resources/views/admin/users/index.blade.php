<x-app-layout>
    <div class="container mx-auto py-8">
        <h2 class="text-2xl font-bold mb-6">User Management</h2>
        <a href="{{ route('admin.users.create') }}" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add New User</a>
        @if(session('status'))
            <div class="mb-4 text-green-600">{{ session('status') }}</div>
        @endif
        <form method="GET" action="" class="mb-6 flex flex-col sm:flex-row items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600 hover:underline">Clear</a>
            @endif
        </form>
        <table class="min-w-full divide-y divide-gray-200 mb-8 shadow rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Roles</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
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
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:underline font-semibold">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline ml-2 font-semibold" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="flex justify-center">
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>
</x-app-layout> 