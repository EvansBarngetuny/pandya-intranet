<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Memo Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 20px; font-weight: bold; color: #16a34a; }
        .stats-grid { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .stat-item { text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #16a34a; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #16a34a; color: white; padding: 10px; text-align: left; }
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
        <div class="title">Memo Statistics Report</div>
        <div>Generated: {{ now()->format('F d, Y h:i A') }}</div>
    </div>

    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number">{{ $memoStats['total'] }}</div>
            <div class="stat-label">Total Memos</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $memoStats['published'] }}</div>
            <div class="stat-label">Published</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $memoStats['acknowledgment_rate'] }}%</div>
            <div class="stat-label">Acknowledgment Rate</div>
        </div>
    </div>

    <table>
        <thead><tr><th>Priority</th><th>Count</th><th>Percentage</th></tr></thead>
        <tbody>
            @foreach($memoStats['by_priority'] as $priority)
            <tr>
                <td>{{ ucfirst($priority->priority) }}</td>
                <td>{{ $priority->count }}</td>
                <td>{{ round(($priority->count / max($memoStats['total'], 1)) * 100, 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Generated from PICS</div>
</body>
</html>
