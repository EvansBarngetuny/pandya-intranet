<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document Audit Trail - {{ $departmentName }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 18px; font-weight: bold; color: #0d9488; }
        .info-box { background: #f3f4f6; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0d9488; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; font-size: 11px; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; }
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
        <div class="title">Document Audit Trail Report</div>
        <div class="subtitle">{{ $departmentName }} Department</div>
        <div>{{ $document->title }}</div>
        <div>Generated: {{ $generated_at }}</div>
    </div>

    <div class="info-box">
        <strong>Document Details:</strong><br>
        File: {{ $document->file_name }}<br>
        Version: {{ $document->version }}<br>
        Department: {{ $departmentName }}
    </div>

    <table>
        <thead>
            <tr><th>Staff Name</th><th>Viewed</th><th>Viewed At</th><th>Acknowledged</th><th>Acknowledged At</th><th>Downloaded</th></tr>
        </thead>
        <tbody>
            @foreach($auditTrail as $record)
            <tr>
                <td>{{ $record['user']->name }}</td>
                <td>{{ $record['viewed_at'] ? 'Yes' : 'No' }}</td>
                <td>{{ $record['viewed_at'] ? \Carbon\Carbon::parse($record['viewed_at'])->format('Y-m-d h:i A') : '-' }}</td>
                <td>{{ $record['acknowledged_at'] ? 'Yes' : 'No' }}</td>
                <td>{{ $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('Y-m-d h:i A') : '-' }}</td>
                <td>{{ $record['downloaded'] ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Generated from PICS - {{ $departmentName }} Department</div>
</body>
</html>
