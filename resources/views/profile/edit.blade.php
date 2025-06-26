@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                    {{ __('Profile') }}
                </h2>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            <div class="mt-4">
                <h2 class="font-semibold">Roles</h2>
                <ul>
                    @foreach($user->roles as $role)
                        <li>{{ $role->name }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="mt-4">
                <h2 class="font-semibold">Permissions</h2>
                <ul>
                    @foreach($user->permissions() as $permission)
                        <li>{{ $permission->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
