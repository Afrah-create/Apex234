<style>
  html, body {
    overflow: auto !important;
    height: auto !important;
  }
</style>
<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        
        <div class="heading">
            <h1 class="header text-center", style="font-weight: bold">Create An Account</h1>
        </div>
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Register as')" />
            <select id="role" name="role" class="block mt-1 w-full" required>
                <option value="retailer" {{ old('role') == 'retailer' ? 'selected' : '' }}>Retailer</option>
                <option value="supplier" {{ old('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Vendor-specific fields (initially hidden) -->
        <div id="vendor-fields" class="mt-4" style="display: none;">
            <div class="mt-4">
                <x-input-label for="business_name" :value="__('Business Name')" />
                <x-text-input id="business_name" class="block mt-1 w-full" type="text" name="business_name" :value="old('business_name')" />
                <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="business_address" :value="__('Business Address')" />
                <textarea id="business_address" name="business_address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('business_address') }}</textarea>
                <x-input-error :messages="$errors->get('business_address')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="phone_number" :value="__('Phone Number')" />
                <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" />
                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="tax_id" :value="__('Tax ID (Optional)')" />
                <x-text-input id="tax_id" class="block mt-1 w-full" type="text" name="tax_id" :value="old('tax_id')" />
                <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="business_license" :value="__('Business License (Optional)')" />
                <x-text-input id="business_license" class="block mt-1 w-full" type="text" name="business_license" :value="old('business_license')" />
                <x-input-error :messages="$errors->get('business_license')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="description" :value="__('Business Description (Optional)')" />
                <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const vendorFields = document.getElementById('vendor-fields');
            
            function toggleVendorFields() {
                if (roleSelect.value === 'vendor') {
                    vendorFields.style.display = 'block';
                } else {
                    vendorFields.style.display = 'none';
                }
            }
            
            // Initial check
            toggleVendorFields();
            
            // Listen for changes
            roleSelect.addEventListener('change', toggleVendorFields);
        });
    </script>
    
</x-guest-layout>
