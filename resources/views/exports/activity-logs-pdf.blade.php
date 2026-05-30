{{-- resources/views/exports/activity-logs-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Activity Logs Export</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            @if(file_exists(public_path('images/pandya-logo.png')))
                <img src="{{ public_path('images/pandya-logo.png') }}" class="logo" alt="Pandya Memorial Hospital">
            @elseif(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Pandya Memorial Hospital">
            @else
                <div style="font-size: 24px;">🏥</div>
            @endif
        </div>
        <h1>Activity Logs Report</h1>
        <p>Generated on: {{ $exported_at->format('F j, Y g:i A') }}</p>
    </div>

    @if($filters['search'] || $filters['module'] || $filters['action'] || $filters['date_from'] || $filters['date_to'])
    <div class="filters">
        <strong>Applied Filters:</strong><br>
        @if($filters['search']) Search: {{ $filters['search'] }} | @endif
        @if($filters['module']) Module: {{ ucfirst($filters['module']) }} | @endif
        @if($filters['action']) Action: {{ str_replace('_', ' ', ucfirst($filters['action'])) }} | @endif
        @if($filters['date_from']) From: {{ $filters['date_from'] }} | @endif
        @if($filters['date_to']) To: {{ $filters['date_to'] }} | @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date/Time</th>
                <th>User</th>
                <th>Module</th>
                <th>Action</th>
                <th>Description</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $log->user->name ?? 'System' }}</td>
                <td>{{ ucfirst($log->module) }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($log->action)) }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->ip_address ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Records: {{ $logs->count() }} | Exported by {{ auth()->user()->name ?? 'System' }}</p>
    </div>
</body>
</html>
