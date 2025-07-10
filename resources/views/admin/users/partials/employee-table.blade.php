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

<div class="mt-6 bg-white rounded-lg shadow-md p-4 flex flex-col items-center gap-4">
    <h3 class="text-lg font-semibold text-gray-800 mb-2 text-center">Export workforce distribution report</h3>
    <form id="employee-assignment-report-form" class="flex flex-col sm:flex-row sm:items-end gap-4 justify-center" method="GET" action="">
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from', date('Y-m-01')) }}" class="border rounded px-2 py-1 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to', date('Y-m-d')) }}" class="border rounded px-2 py-1 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Vendor</label>
            <select name="vendor_id" class="border rounded px-2 py-1 text-sm">
                <option value="">All Vendors</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex space-x-2 mt-2 sm:mt-0">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">Filter</button>
        </div>
    </form>
    <div class="flex flex-col sm:flex-row sm:items-center gap-2 justify-center">
        <div class="flex space-x-2 justify-center">
            <a href="{{ route('admin.employees.export-csv', request()->all()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm" target="_blank">Export CSV</a>
            <a href="{{ route('admin.employees.export-pdf', request()->all()) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm" target="_blank">Export PDF</a>
        </div>
    </div>
</div> 