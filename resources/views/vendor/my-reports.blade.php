@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">My Reports</h1>
    <div class="card">
        <div class="card-body">
            <table class="table" id="vendor-reports-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Reports will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/vendor/my-reports')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#vendor-reports-table tbody');
            tbody.innerHTML = '';
            data.data.forEach(report => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${report.scheduled_report_id ?? ''}</td>
                    <td>${report.report_type ?? ''}</td>
                    <td>${report.generated_at ?? ''}</td>
                    <td>${report.file_path ? `<a href='/storage/${report.file_path}' target='_blank'>Download</a>` : ''}</td>
                `;
                tbody.appendChild(tr);
            });
        });
});
</script>
@endsection 