@extends('layouts.app')

@section('content')
<div class="main-content p-6">
    <h2 class="text-2xl font-bold mb-6">User Management</h2>
    <div x-data="{ tab: 'users' }">
        <nav class="flex border-b mb-4">
            <button :class="tab === 'users' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'" class="px-4 py-2 font-medium focus:outline-none" @click="tab = 'users'">Users</button>
            <button :class="tab === 'workforce' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'" class="px-4 py-2 font-medium focus:outline-none" @click="tab = 'workforce'">Workforce</button>
            <button :class="tab === 'distribution' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'" class="px-4 py-2 font-medium focus:outline-none" @click="tab = 'distribution'">Distribution Centers</button>
        </nav>

        <div x-show="tab === 'users'">
            @include('admin.users.partials.user-table', ['users' => $users])
        </div>
        <div x-show="tab === 'workforce'">
            @include('admin.users.partials.employee-table', ['employees' => $employees, 'vendors' => $vendors])
        </div>
        <div x-show="tab === 'distribution'">
            @include('admin.distribution-centers.partials.table', ['centers' => $distributionCenters])
        </div>
    </div>

    {{-- Vendor Applicants Awaiting Approval --}}
    @if(isset($vendorApplicants) && $vendorApplicants->count())
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Vendor Applicants Awaiting Approval</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($vendorApplicants as $applicant)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $applicant->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $applicant->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $applicant->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $applicant->company_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($applicant->status) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form method="POST" action="{{ route('admin.vendor-applicants.approve', $applicant->id) }}">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Confirm</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection 