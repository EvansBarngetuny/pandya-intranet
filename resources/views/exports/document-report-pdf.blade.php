<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 20px; font-weight: bold; color: #0d9488; }
        .stats-grid { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .stat-item { text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #0d9488; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0d9488; color: white; padding: 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; }
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
        <div class="title">Document Statistics Report</div>
        <div>Generated: {{ now()->format('F d, Y h:i A') }}</div>
    </div>

    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number">{{ $documentStats['total'] }}</div>
            <div class="stat-label">Total Documents</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $documentStats['active'] }}</div>
            <div class="stat-label">Active Documents</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $documentStats['total_downloads'] }}</div>
            <div class="stat-label">Total Downloads</div>
        </div>
    </div>

    <table>
        <thead><tr><th>Category</th><th>Count</th></tr></thead>
        <tbody>
            @foreach($documentStats['by_category'] as $category)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $category->category)) }}</td>
                <td>{{ $category->count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Generated from PICS</div>
</body>
</html>
