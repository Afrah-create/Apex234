@extends('layouts.app')

@section('content')
    <main class="main-content flex justify-center items-center">
        <div class="max-w-xl w-full bg-white p-8 rounded-lg shadow mt-10">
            <h2 class="text-2xl font-bold mb-6 text-center">Edit User</h2>
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block font-semibold mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Roles</label>
                    <div class="flex flex-wrap gap-4">
                        @foreach($roles as $role)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" @if($user->roles->contains($role->id)) checked @endif class="form-checkbox h-5 w-5 text-blue-600">
                                <span class="ml-2">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-between items-center mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">Update</button>
                    <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Back to User List</a>
                </div>
            </form>
        </div>
    </main>
@endsection 