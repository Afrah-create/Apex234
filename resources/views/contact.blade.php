@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Contact Us</h1>
    <div class="bg-white p-6 rounded shadow mb-8">
        <p class="mb-4">Have questions, feedback, or need support? Fill out the form below or reach us directly at <a href="mailto:support@example.com" class="text-blue-600 underline">support@example.com</a>.</p>
        <form method="POST" action="#" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block font-semibold mb-1">Name</label>
                <input type="text" id="name" name="name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label for="email" class="block font-semibold mb-1">Email</label>
                <input type="email" id="email" name="email" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label for="message" class="block font-semibold mb-1">Message</label>
                <textarea id="message" name="message" rows="5" class="w-full border rounded px-3 py-2" required></textarea>
            </div>
            <button type="submit" class="bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-800">Send Message</button>
        </form>
    </div>
    <div class="bg-gray-50 p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-2">Contact Information</h2>
        <p><strong>Email:</strong> <a href="mailto:support@example.com" class="text-blue-600 underline">support@example.com</a></p>
        <p><strong>Phone:</strong> +1 (555) 123-4567</p>
        <p><strong>Address:</strong> 123 Main Street, Cityville, Country</p>
    </div>
</div>
@endsection 