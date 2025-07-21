@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="overflow-x-auto w-full">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 sm:px-4 sm:py-2 text-left text-xs sm:text-sm font-bold text-gray-600 uppercase">Name</th>
                <th class="px-2 py-1 sm:px-4 sm:py-2 text-left text-xs sm:text-sm font-bold text-gray-600 uppercase">Role</th>
                <th class="px-2 py-1 sm:px-4 sm:py-2 text-left text-xs sm:text-sm font-bold text-gray-600 uppercase">Assigned Vendor</th>
                <th class="px-2 py-1 sm:px-4 sm:py-2 text-left text-xs sm:text-sm font-bold text-gray-600 uppercase">Assigned Distribution Center</th>
                <th class="px-2 py-1 sm:px-4 sm:py-2 text-left text-xs sm:text-sm font-bold text-gray-600 uppercase">Action</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @foreach($employees as $employee)
            <tr>
                <td class="px-2 py-1 sm:px-4 sm:py-2 text-gray-900 text-xs sm:text-sm">{{ $employee->name }}</td>
                <td class="px-2 py-1 sm:px-4 sm:py-2 text-gray-700 text-xs sm:text-sm">{{ $employee->role }}</td>
                <td class="px-2 py-1 sm:px-4 sm:py-2 text-gray-700 text-xs sm:text-sm">
                    <form method="POST" action="{{ route('admin.employees.assignVendor', $employee) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                        @csrf
                        <select name="vendor_id" class="border rounded px-2 py-1 text-xs sm:text-sm">
                            <option value="">-- Unassigned --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @if($employee->vendor_id == $vendor->id) selected @endif>
                                    {{ $vendor->business_name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="distribution_center_id" class="border rounded px-2 py-1 text-xs sm:text-sm">
                            <option value="">-- Unassigned DC --</option>
                            @foreach($distributionCenters as $dc)
                                <option value="{{ $dc->id }}" @if($employee->distribution_center_id == $dc->id) selected @endif>{{ $dc->center_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition text-xs sm:text-sm">Assign</button>
                    </form>
                </td>
                <td class="px-2 py-1 sm:px-4 sm:py-2 text-gray-700 text-xs sm:text-sm">
                    @if($employee->distributionCenter)
                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $employee->distributionCenter->center_name }}</span>
                    @else
                        <span class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Unassigned</span>
                    @endif
                </td>
                <td class="px-2 py-1 sm:px-4 sm:py-2 text-gray-700 flex flex-col sm:flex-row gap-2 text-xs sm:text-sm">
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