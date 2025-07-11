<form method="POST" action="{{ route('admin.employees.store') }}" class="mb-6 bg-white p-4 rounded shadow">
    @csrf
    <div class="flex flex-wrap gap-4">
        <input name="name" placeholder="Name" required class="border rounded px-2 py-1" />
        <input name="email" type="email" placeholder="Email" required class="border rounded px-2 py-1" />
        <input name="password" type="password" placeholder="Password" required class="border rounded px-2 py-1" />
        <input name="password_confirmation" type="password" placeholder="Confirm Password" required class="border rounded px-2 py-1" />
        <select name="role" required class="border rounded px-2 py-1">
            <option value="">Select Role</option>
            <option>Production Worker</option>
            <option>Warehouse Staff</option>
            <option>Driver</option>
            <option>Sales Manager</option>
        </select>
        <select name="vendor_id" class="border rounded px-2 py-1">
            <option value="">Unassigned</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}">{{ $vendor->business_name }}</option>
            @endforeach
        </select>
        <select name="status" required class="border rounded px-2 py-1">
            <option>Active</option>
            <option>On Leave</option>
            <option>Terminated</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">Add Employee</button>
    </div>
</form>

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
                                    {{ $vendor->business_name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">Assign</button>
                    </form>
                </td>
                <td class="px-4 py-2 text-gray-700 flex gap-2">
                    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 transition">Edit</a>
                    <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee?');">
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