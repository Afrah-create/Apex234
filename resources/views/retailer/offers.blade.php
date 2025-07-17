@extends('layouts.app')

@section('content')
<main class="main-content bg-gradient-to-br from-orange-50 via-yellow-50 to-white min-h-screen py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-2xl p-10 text-center">
            <h2 class="text-4xl font-extrabold text-orange-600 mb-4 tracking-tight drop-shadow">Special Offers</h2>
            <p class="text-lg text-gray-700 mb-6">
                <span class="block mb-4">Check back soon for exclusive deals and promotions tailored just for you!</span>
                <span class="block bg-orange-50 border-l-4 border-orange-400 p-4 rounded-xl text-base text-orange-800 font-semibold shadow-sm">
                    <strong>Special Offer:</strong> Whenever you buy <span class="font-bold text-orange-600">100 or more units</span> of any product, you stand a chance of winning a <span class="font-bold text-green-600">30% discount</span> on your next order!<br><br>
                    <span class="block">All you have to do is:</span>
                    <ul class="list-disc list-inside text-sm mt-2 mb-2">
                        <li>Take a screenshot of your order and order history.</li>
                        <li>On delivery, take a picture with your delivered products.</li>
                        <li>Send both images to <span class="font-bold text-blue-700">+256 2009053205</span>.</li>
                    </ul>
                    <span class="block mt-2">Our team will verify and apply your discount on your next purchase. Good luck!</span>
                </span>
            </p>
            <svg class="mx-auto w-24 h-24 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 10c-4.418 0-8-1.79-8-4V6a2 2 0 012-2h12a2 2 0 012 2v8c0 2.21-3.582 4-8 4z"/></svg>
        </div>
    </div>
</main>
@endsection 