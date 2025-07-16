@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">My Reports</h1>
    <div class="card">
        <div class="card-body">
            <table class="table" id="supplier-reports-table">
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
    fetch('/supplier/my-reports')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#supplier-reports-table tbody');
            tbody.innerHTML = '';
            if (data.data && data.data.length > 0) {
                data.data.forEach(report => {
                    tbody.innerHTML += `<tr>
                        <td>${report.name || ''}</td>
                        <td>${report.report_type || ''}</td>
                        <td>${report.created_at ? new Date(report.created_at).toLocaleString() : ''}</td>
                        <td>${report.file_path ? `<a href='/storage/${report.file_path}' target='_blank' class='text-blue-600 underline'>Download</a>` : 'Not available'}</td>
                    </tr>`;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No reports found.</td></tr>';
            }
        });
});
</script>
@endsection 