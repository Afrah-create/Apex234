<h2>Scheduled Report: {{ $scheduledReport->name }}</h2>
<p><strong>Description:</strong> {{ $scheduledReport->description }}</p>
<hr>
<div>
    <strong>Report Data:</strong>
    @if(is_array($reportData))
        <pre>{{ json_encode($reportData, JSON_PRETTY_PRINT) }}</pre>
    @else
        <pre>{{ $reportData }}</pre>
    @endif
</div> 