@extends('layouts.app')

@section('title', 'Terms of Use')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Terms of Use</h1>
    <div class="bg-white p-6 rounded shadow">
        <p>Effective Date: {{ date('F d, Y') }}</p>
        <p class="mt-4">By accessing or using our platform, you agree to be bound by these Terms of Use. If you do not agree to these terms, please do not use our services.</p>
        <h2 class="text-xl font-semibold mt-6 mb-2">1. Use of the Platform</h2>
        <ul class="list-disc ml-6">
            <li>You must be at least 18 years old or have legal parental or guardian consent to use this platform.</li>
            <li>You agree to provide accurate and complete information when registering and using the platform.</li>
            <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
        </ul>
        <h2 class="text-xl font-semibold mt-6 mb-2">2. Prohibited Activities</h2>
        <ul class="list-disc ml-6">
            <li>Using the platform for any unlawful purpose.</li>
            <li>Attempting to gain unauthorized access to other accounts or systems.</li>
            <li>Transmitting any viruses, malware, or harmful code.</li>
        </ul>
        <h2 class="text-xl font-semibold mt-6 mb-2">3. Intellectual Property</h2>
        <p>All content, trademarks, and data on this platform are the property of the company or its licensors. You may not use, reproduce, or distribute any content without permission.</p>
        <h2 class="text-xl font-semibold mt-6 mb-2">4. Termination</h2>
        <p>We reserve the right to suspend or terminate your access to the platform at our discretion, without notice, for conduct that we believe violates these Terms or is harmful to other users or the platform.</p>
        <h2 class="text-xl font-semibold mt-6 mb-2">5. Limitation of Liability</h2>
        <p>We are not liable for any indirect, incidental, or consequential damages arising from your use of the platform. The platform is provided "as is" and "as available" without warranties of any kind.</p>
        <h2 class="text-xl font-semibold mt-6 mb-2">6. Changes to Terms</h2>
        <p>We may update these Terms of Use from time to time. Continued use of the platform after changes constitutes acceptance of the new terms.</p>
        <h2 class="text-xl font-semibold mt-6 mb-2">7. Contact Us</h2>
        <p>If you have any questions about these Terms, please contact us at yoghurtcaramel@gmail.com.</p>
    </div>
</div>
@endsection 