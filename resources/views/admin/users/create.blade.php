<x-app-layout>
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow mt-10">
        <h2 class="text-2xl font-bold mb-6 text-center">Add New User</h2>
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="block font-semibold mb-1">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="email" class="block font-semibold mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="mobile" class="block font-semibold mb-1">Mobile</label>
                <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-blue-600" />
                <label for="is_active" class="ml-2 font-semibold">Active</label>
            </div>
            <div>
                <label for="photo_url" class="block font-semibold mb-1">Photo URL</label>
                <input type="url" id="photo_url" name="photo_url" value="{{ old('photo_url') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="flex justify-between items-center mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout> 