@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Privacy Policy</h1>
    <div class="bg-white p-6 rounded shadow">
        <p>Effective Date: {{ date('F d, Y') }}</p>
        <p class="mt-4">This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform. Please read this policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site.</p>
        <h2 class="text-xl font-semibold mt-6 mb-2">1. Information We Collect</h2>
        <ul class="list-disc ml-6">
            <li>Personal Data (name, email, contact information, etc.)</li>
            <li>Usage Data (pages visited, actions taken, etc.)</li>
            <li>Cookies and Tracking Technologies</li>
        </ul>
        <h2 class="text-xl font-semibold mt-6 mb-2">2. How We Use Your Information</h2>
        <ul class="list-disc ml-6">
            <li>To provide and maintain our services</li>
            <li>To improve user experience</li>
            <li>To communicate with you</li>
            <li>To comply with legal obligations</li>
        </ul>
        <h2 class="text-xl font-semibold mt-6 mb-2">3. Sharing Your Information</h2>
        <ul class="list-disc ml-6">
            <li>With service providers who assist us in operating the platform</li>
            <li>When required by law or to protect our rights</li>
        </ul>
        <h2 class="text-xl font-semibold mt-6 mb-2">4. Security of Your Information</h2>
        <p>We use administrative, technical, and physical security measures to help protect your personal information. However, no method of transmission over the Internet or method of electronic storage is 100% secure.</p>
        <h2 class="text-xl font-semibold mt-6 mb-2">5. Your Privacy Rights</h2>
        <ul class="list-disc ml-6">
            <li>You may review, change, or terminate your account at any time.</li>
            <li>You may opt out of certain communications.</li>
        </ul>
        <h2 class="text-xl font-semibold mt-6 mb-2">6. Changes to This Privacy Policy</h2>
        <p>We may update this Privacy Policy from time to time. We will notify you of any changes by updating the effective date at the top of this policy.</p>
        <h2 class="text-xl font-semibold mt-6 mb-2">7. Contact Us</h2>
        <p>If you have any questions or concerns about this Privacy Policy, please contact us at yoghurtcaramel@gmail.com.</p>
    </div>
</div>
@endsection 