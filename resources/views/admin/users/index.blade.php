@extends('layouts.app')

@section('content')
<div class="main-content p-6">
    <h2 class="text-2xl font-bold mb-6">User Management</h2>
    <div x-data="{ tab: 'users' }">
        <nav class="flex border-b mb-4">
            <button :class="tab === 'users' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'" class="px-4 py-2 font-medium focus:outline-none" @click="tab = 'users'">Users</button>
            <button :class="tab === 'workforce' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'" class="px-4 py-2 font-medium focus:outline-none" @click="tab = 'workforce'">Workforce</button>
        </nav>

        <div x-show="tab === 'users'">
            @include('admin.users.partials.user-table', ['users' => $users])
        </div>
        <div x-show="tab === 'workforce'">
            @include('admin.users.partials.employee-table', ['employees' => $employees, 'vendors' => $vendors])
        </div>
    </div>
</div>
@endsection 