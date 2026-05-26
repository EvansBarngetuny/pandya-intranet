<?php

namespace App\Livewire\Memos;

use Livewire\Component;
use App\Models\Memo;
use App\Models\MemoAcknowledgment;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuditTrailExport;

class ShowMemo extends Component
{
    public Memo $memo;
    public $hasAcknowledged = false;
    public $hasRead = false;
    public $readAt = null;
    public $acknowledgedAt = null;
    public $showAuditTrail = false;

    public function mount(Memo $memo)
    {
        $user = auth()->user();

    // Check if user has access to this memo
    if (!$this->userHasAccess($memo, $user)) {
        abort(403, 'You do not have access to this memo.');
    }

    $this->memo = $memo;
    $this->hasRead = $this->memo->isReadBy($user);
    $this->hasAcknowledged = $this->memo->acknowledgedBy($user);

    // Get timestamps
    $readRecord = DB::table('memo_reads')
        ->where('memo_id', $this->memo->id)
        ->where('user_id', $user->id)
        ->first();
    $this->readAt = $readRecord?->read_at;

    $ackRecord = MemoAcknowledgment::where('memo_id', $this->memo->id)
        ->where('user_id', $user->id)
        ->first();
    $this->acknowledgedAt = $ackRecord?->acknowledged_at;

    // Auto-mark as read when viewing (if not already read)
    if ($this->memo->status === 'published' && !$this->hasRead) {
        $this->markAsRead();
    }
    }
    protected function userHasAccess($memo, $user)
{
    // Admin can see all memos
    if ($user->isAdmin()) {
        return true;
    }

    // Only published memos are visible to non-admins
    if ($memo->status !== 'published') {
        return false;
    }

    // Check audience type
    if ($memo->audience_type === 'all') {
        return true;
    }

    if ($memo->audience_type === 'departments') {
        // Check if user's department is in the selected departments
        $departmentIds = collect($memo->audience_ids)->where('type', 'department')->pluck('id')->toArray();
        return in_array($user->department_id, $departmentIds);
    }

    if ($memo->audience_type === 'specific_users') {
        // Check if user is in the selected users list
        $userIds = collect($memo->audience_ids)->where('type', 'user')->pluck('id')->toArray();
        return in_array($user->id, $userIds);
    }

    return false;
}

    public function markAsRead()
    {
        if (!$this->hasRead) {
            DB::table('memo_reads')->insert([
                'memo_id' => $this->memo->id,
                'user_id' => auth()->id(),
                'read_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->hasRead = true;
            $this->readAt = now();

            // Log activity
            $this->logActivity('read');

            session()->flash('info', 'Memo marked as read.');
        }
    }

    public function acknowledge()
    {
        if (!$this->hasAcknowledged && $this->memo->require_acknowledgment && $this->memo->status === 'published') {
            // First ensure it's marked as read
            if (!$this->hasRead) {
                $this->markAsRead();
            }

            // Then acknowledge
            MemoAcknowledgment::create([
                'memo_id' => $this->memo->id,
                'user_id' => auth()->id(),
                'acknowledged_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $this->hasAcknowledged = true;
            $this->acknowledgedAt = now();

            // Log activity
            $this->logActivity('acknowledged');

            session()->flash('message', 'Memo acknowledged successfully! This serves as your digital signature.');
        }
    }

    protected function logActivity($action)
    {
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action . '_memo',
            'module' => 'memo',
            'description' => "User {$action} memo: {$this->memo->memo_number} - {$this->memo->title}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    public function toggleAuditTrail()
    {
        $this->showAuditTrail = !$this->showAuditTrail;
    }
    public function downloadMemo()
    {
        // Implement PDF generation logic here, using a package like barryvdh/laravel-dompdf
        $html = view('memos.memo-pdf', ['memo' => $this->memo])->render();
        $fileman = 'memo_' . $this->memo->memo_number . '_' . now()->format('Ymd_His') . '.html';
        // For example:
        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, $fileman);
        // $pdf = PDF::loadView('memos.pdf', ['memo' => $this->memo]);
        // return $pdf->download("Memo-{$this->memo->memo_number}.pdf");
    }
    public function exportAuditTrailPDF()
{
    $auditTrail = $this->memo->getAllUsersAuditTrail();

    $data = [
        'memo' => $this->memo,
        'auditTrail' => $auditTrail,
        'generated_at' => now()->format('F d, Y h:i A'),
    ];

    $pdf = Pdf::loadView('exports.audit-trail-pdf', $data);
    $filename = "audit_trail_{$this->memo->memo_number}_{$this->memo->title}.pdf";

    return $pdf->download($filename);
}

public function exportAuditTrailExcel()
{
    $auditTrail = $this->memo->getAllUsersAuditTrail();

    $exportData = [];
    foreach ($auditTrail as $record) {
        $exportData[] = [
            'Staff Name' => $record['user']->name,
            'Department' => $record['user']->department->name ?? 'N/A',
            'Staff Number' => $record['user']->staff_number,
            'Role' => ucfirst($record['user']->role),
            'Read Status' => $record['read_at'] ? 'Read' : 'Not Read',
            'Read At' => $record['read_at'] ? \Carbon\Carbon::parse($record['read_at'])->format('Y-m-d h:i A') : '-',
            'Acknowledged Status' => $record['acknowledged_at'] ? 'Acknowledged' : 'Pending',
            'Acknowledged At' => $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('Y-m-d h:i A') : '-',
        ];
    }

    $filename = "audit_trail_{$this->memo->memo_number}_{$this->memo->title}.xlsx";

    return Excel::download(new class($exportData) implements \Maatwebsite\Excel\Concerns\FromArray {
        private $data;

        public function __construct($data)
        {
            $this->data = $data;
        }

        public function array(): array
        {
            return $this->data;
        }
    }, $filename);
}
    public function render()
    {
        return view('livewire.memos.show-memo', [
            'readAt' => $this->readAt,
            'acknowledgedAt' => $this->acknowledgedAt,
        ])->layout('layouts.app');
    }
}
