@extends('layouts.app')

@section('content')
<div class="user-management-container main-content" style="flex:1; min-width:0;">
    <h2 class="user-management-header">Manage Users</h2>
    <div class="user-management-bar">
        <form method="GET" action="">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." />
            <button type="submit">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600 hover:underline">Clear</a>
                @endif
            </form>
        <a href="{{ route('admin.users.create') }}" class="inline-block">Add New User</a>
        </div>
    <div class="user-table-container">
        <table class="user-table">
            <thead>
                    <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Actions</th>
                    </tr>
                </thead>
            <tbody>
                    @foreach($users as $user)
                    <tr class="@if($loop->even) bg-gray-50 @endif">
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="action-btn edit-btn">Edit</a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    <div class="flex justify-center mt-4">
        {{ $users->links('pagination::simple-tailwind') }}
        </div>
</div>
@endsection 