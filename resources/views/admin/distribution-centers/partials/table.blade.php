@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
@endif
<div class="mb-4 flex justify-end">
    <a href="{{ route('admin.distribution-centers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Distribution Center</a>
</div>
<div class="overflow-x-auto w-full">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 text-left text-xs font-bold text-gray-600 uppercase">Name</th>
                <th class="px-2 py-1 text-left text-xs font-bold text-gray-600 uppercase">Code</th>
                <th class="px-2 py-1 text-left text-xs font-bold text-gray-600 uppercase">Manager</th>
                <th class="px-2 py-1 text-left text-xs font-bold text-gray-600 uppercase">Type</th>
                <th class="px-2 py-1 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                <th class="px-2 py-1 text-left text-xs font-bold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @foreach($centers as $center)
            <tr>
                <td class="px-2 py-1 text-gray-900">{{ $center->center_name }}</td>
                <td class="px-2 py-1 text-gray-700">{{ $center->center_code }}</td>
                <td class="px-2 py-1 text-gray-700">{{ $center->center_manager }}</td>
                <td class="px-2 py-1 text-gray-700">{{ ucfirst($center->center_type) }}</td>
                <td class="px-2 py-1 text-gray-700">{{ ucfirst($center->status) }}</td>
                <td class="px-2 py-1 text-gray-700 flex gap-2">
                    <a href="{{ route('admin.distribution-centers.edit', $center->id) }}" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 transition">Edit</a>
                    <form action="{{ route('admin.distribution-centers.destroy', $center->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this center?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $centers->links() }}</div> 