<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Audit Trail - {{ $memo->memo_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .memo-number {
            color: #666;
            font-size: 14px;
        }
        .info-box {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #4f46e5;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        .status-read {
            color: green;
            font-weight: bold;
        }
        .status-unread {
            color: red;
            font-weight: bold;
        }
        .status-acknowledged {
            color: green;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .summary {
            background: #f0fdf4;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Memo Audit Trail Report</div>
        <div class="memo-number">{{ $memo->memo_number }} - {{ $memo->title }}</div>
        <div>Generated on: {{ $generated_at }}</div>
    </div>
    
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Memo Title:</span>
            <span>{{ $memo->title }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Memo Number:</span>
            <span>{{ $memo->memo_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Created By:</span>
            <span>{{ $memo->creator->name ?? 'Unknown' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Published Date:</span>
            <span>{{ $memo->published_at ? \Carbon\Carbon::parse($memo->published_at)->format('F d, Y h:i A') : 'Not published' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Target Audience:</span>
            <span>{{ $memo->formatted_audience }}</span>
        </div>
    </div>
    
    <h3>Staff Acknowledgment Records</h3>
    
    <table>
        <thead>
            <tr>
                <th>Staff Name</th>
                <th>Department</th>
                <th>Staff Number</th>
                <th>Role</th>
                <th>Read Status</th>
                <th>Read At</th>
                <th>Acknowledged Status</th>
                <th>Acknowledged At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($auditTrail as $record)
                <tr>
                    <td>{{ $record['user']->name }}</td>
                    <td>{{ $record['user']->department->name ?? 'N/A' }}</td>
                    <td>{{ $record['user']->staff_number }}</td>
                    <td>{{ ucfirst($record['user']->role) }}</td>
                    <td class="{{ $record['read_at'] ? 'status-read' : 'status-unread' }}">
                        {{ $record['read_at'] ? 'Read' : 'Not Read' }}
                    </td>
                    <td>{{ $record['read_at'] ? \Carbon\Carbon::parse($record['read_at'])->format('Y-m-d h:i A') : '-' }}</td>
                    <td class="{{ $record['acknowledged_at'] ? 'status-acknowledged' : 'status-pending' }}">
                        {{ $record['acknowledged_at'] ? 'Acknowledged' : 'Pending' }}
                    </td>
                    <td>{{ $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('Y-m-d h:i A') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="summary">
        <strong>Summary:</strong><br>
        Total Staff: {{ count($auditTrail) }}<br>
        Read: {{ collect($auditTrail)->where('read_at', '!=', null)->count() }}<br>
        Acknowledged: {{ collect($auditTrail)->where('acknowledged_at', '!=', null)->count() }}<br>
        Completion Rate: {{ $memo->acknowledgment_percentage }}%
    </div>
    
    <div class="footer">
        This is a system-generated report from Pandya Internal Communication System (PICS)<br>
        Generated on {{ $generated_at }}
    </div>
</body>
</html>