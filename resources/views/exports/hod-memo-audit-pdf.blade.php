<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Memo Audit Trail - {{ $departmentName }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 18px; font-weight: bold; color: #16a34a; }
        .subtitle { font-size: 14px; color: #4b5563; }
        .info-box { background: #f3f4f6; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #16a34a; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; font-size: 11px; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #6b7280; }
        .badge-read { color: #059669; font-weight: bold; }
        .badge-unread { color: #dc2626; font-weight: bold; }
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
        <div class="title">Memo Audit Trail Report</div>
        <div class="subtitle">{{ $departmentName }} Department</div>
        <div>{{ $memo->title }} ({{ $memo->memo_number }})</div>
        <div>Generated: {{ $generated_at }}</div>
    </div>

    <div class="info-box">
        <strong>Memo Details:</strong><br>
        Created By: {{ $memo->creator->name ?? 'Unknown' }}<br>
        Published: {{ $memo->published_at ? \Carbon\Carbon::parse($memo->published_at)->format('Y-m-d') : 'Not published' }}<br>
        Department: {{ $departmentName }}
    </div>

    <table>
        <thead>
            <tr><th>Staff Name</th><th>Read Status</th><th>Read At</th><th>Acknowledged</th><th>Acknowledged At</th></tr>
        </thead>
        <tbody>
            @foreach($auditTrail as $record)
            <tr>
                <td>{{ $record['user']->name }}</td>
                <td>{{ $record['read_at'] ? 'Read' : 'Not Read' }}</td>
                <td>{{ $record['read_at'] ? \Carbon\Carbon::parse($record['read_at'])->format('Y-m-d h:i A') : '-' }}</td>
                <td>{{ $record['acknowledged_at'] ? 'Acknowledged' : 'Pending' }}</td>
                <td>{{ $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('Y-m-d h:i A') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Generated from Pandya Internal Communication System (PICS) - {{ $departmentName }} Department</div>
</body>
</html>
