<div class="bg-white rounded-lg shadow-md p-4">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Name</th>
                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Role</th>
                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Assigned Vendor</th>
                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Action</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @foreach($employees as $employee)
            <tr>
                <td class="px-4 py-2 text-gray-900">{{ $employee->name }}</td>
                <td class="px-4 py-2 text-gray-700">{{ $employee->role }}</td>
                <td class="px-4 py-2 text-gray-700">
                    <form method="POST" action="{{ route('admin.employees.assignVendor', $employee) }}" class="flex items-center space-x-2">
                        @csrf
                        <select name="vendor_id" class="border rounded px-2 py-1">
                            <option value="">-- Unassigned --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @if($employee->vendor_id == $vendor->id) selected @endif>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">Assign</button>
                    </form>
                </td>
                <td class="px-4 py-2 text-gray-700">
                    @if($employee->vendor)
                        <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Assigned</span>
                    @else
                        <span class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Unassigned</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div> 