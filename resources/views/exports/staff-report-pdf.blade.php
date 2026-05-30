<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 20px; font-weight: bold; color: #1e40af; }
        .subtitle { color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #2563eb; color: white; padding: 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
        .stats-box { background: #f3f4f6; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .stats-grid { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .stat-item { text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #2563eb; }
        .stat-label { font-size: 12px; color: #666; }
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
        <div class="title">Pandya Memorial Hospital</div>
        <div class="subtitle">Staff Statistics Report</div>
        <div>Generated: {{ now()->format('F d, Y h:i A') }}</div>
    </div>

    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number">{{ $staffStats['total'] }}</div>
            <div class="stat-label">Total Staff</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $staffStats['new_this_month'] }}</div>
            <div class="stat-label">New This Month</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $staffStats['by_role']->where('role', 'admin')->first()?->count ?? 0 }}</div>
            <div class="stat-label">Administrators</div>
        </div>
    </div>

    <table>
        <thead>
            <tr><th>Department</th><th>Staff Count</th><th>Percentage</th></tr>
        </thead>
        <tbody>
            @foreach($staffStats['by_department'] as $dept)
            <tr>
                <td>{{ $dept->department->name ?? 'No Department' }}</td>
                <td>{{ $dept->count }}</td>
                <td>{{ round(($dept->count / max($staffStats['total'], 1)) * 100, 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        This report was generated from Pandya Internal Communication System (PICS)
    </div>
</body>
</html>
