@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-4">
    <h1 class="text-3xl font-bold mb-6 text-blue-800">Help & Support - Frequently Asked Questions</h1>
    <h2 class="text-2xl font-bold mb-4">Help & Support</h2>
    <p>If you need assistance, please contact support or check the documentation.</p>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">How do I reset my password?</h2>
            <p class="text-gray-700 mt-2">Go to the login page and click on "Forgot Password?". Enter your email address and follow the instructions sent to your email.</p>
        </div>
        <div>
            <h2 class="text-xl font-semibold text-gray-800">How to update my profile information?</h2>
            <p class="text-gray-700 mt-2">After logging in, click on your profile icon in the top right corner and select "Profile". You can update your name, email, and profile photo from there.</p>
        </div>
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Who to contact for technical support?</h2>
            <p class="text-gray-700 mt-2">If you need further assistance, please email our support team at <a href="mailto:yoghurtcaramel@gmail.com" class="text-blue-600 underline">yoghurtcaramel@gmail.com</a>.</p>
        </div>
        <div>
            <h2 class="text-xl font-semibold text-gray-800">How to register as a vendor, supplier, or retailer?</h2>
            <p class="text-gray-700 mt-2">Visit the registration page and select your role. Fill in the required details and follow the on-screen instructions.</p>
        </div>
        <div>
            <h2 class="text-xl font-semibold text-gray-800">How toview my dashboard?</h2>
            <p class="text-gray-700 mt-2">After logging in, you will be redirected to your role-specific dashboard. You can also access it from the navigation menu.</p>
        </div>
        <div>
            <h2 class="text-xl font-semibold text-gray-800">How doreport a bug or suggest a feature?</h2>
            <p class="text-gray-700 mt-2">Please email your feedback to <a href="mailto:yoghurtcaramel@gmail.com" class="text-blue-600 underline">yoghurtcaramel@gmail.com</a> with a description of the issue or suggestion.</p>
        </div>
    </div>
</div>
@endsection 