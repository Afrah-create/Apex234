@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Edit User</h2>
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="max-w-lg bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Roles</label>
            <div class="flex flex-wrap gap-4">
                @foreach($roles as $role)
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" @if($user->roles->contains($role->id)) checked @endif>
                        <span class="ml-2">{{ $role->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Back to User List</a>
        </div>
    </form>
</div>
@endsection 