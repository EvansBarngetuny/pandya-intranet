<?php

namespace App\Livewire\Hod;

use Livewire\Component;
use App\Models\User;
use App\Models\Memo;
use App\Models\Document;
use App\Models\MemoAcknowledgment;
use App\Models\DocumentAcknowledgment;
use App\Models\DocumentView;
use App\Models\DocumentDownload;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class DepartmentReports extends Component
{
    public $departmentId;
    public $departmentName;
    public $dateRange = 'month';
    public $stats = [];
    public $memoStats = [];
    public $documentStats = [];
    public $staffPerformance = [];
    public $showMemoAudit = false;
    public $showDocumentAudit = false;
    public $selectedMemo = null;
    public $selectedDocument = null;
    public $memoAuditTrail = [];
    public $documentAuditTrail = [];

    // Helper method to sanitize filename
    private function sanitizeFilename($filename)
    {
        $filename = preg_replace('/[^a-zA-Z0-9._\- ]/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename);
        $filename = str_replace(' ', '_', $filename);
        $filename = trim($filename, '_');
        return substr($filename, 0, 200);
    }

    public function mount()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->department_id) {
            abort(403, 'You are not assigned to any department. Please contact administrator.');
        }

        $this->departmentId = $user->department_id;
        $this->departmentName = $user->department ? $user->department->name : 'Your Department';

        $this->loadStats();
        $this->loadMemoStats();
        $this->loadDocumentStats();
        $this->loadStaffPerformance();
    }

    public function loadStats()
    {
        try {
            $this->stats = [
                'total_staff' => User::where('department_id', $this->departmentId)
                    ->where('is_active', true)
                    ->count(),
                'total_memos' => Memo::where('department_id', $this->departmentId)
                    ->where('status', 'published')
                    ->count(),
                'published_memos' => Memo::where('department_id', $this->departmentId)
                    ->where('status', 'published')
                    ->whereMonth('published_at', now()->month)
                    ->count(),
                'acknowledgment_rate' => $this->getDepartmentAcknowledgmentRate(),
                'active_staff' => User::where('department_id', $this->departmentId)
                    ->where('is_active', true)
                    ->count(),
                'inactive_staff' => User::where('department_id', $this->departmentId)
                    ->where('is_active', false)
                    ->count(),
            ];
        } catch (\Exception $e) {
            $this->stats = [
                'total_staff' => 0,
                'total_memos' => 0,
                'published_memos' => 0,
                'acknowledgment_rate' => 0,
                'active_staff' => 0,
                'inactive_staff' => 0,
            ];
        }
    }

    public function loadMemoStats()
    {
        try {
            $this->memoStats['by_priority'] = Memo::where('department_id', $this->departmentId)
                ->select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get();

            $this->memoStats['monthly_trend'] = Memo::where('department_id', $this->departmentId)
                ->where('status', 'published')
                ->select(DB::raw('MONTH(published_at) as month'), DB::raw('count(*) as count'))
                ->whereYear('published_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $this->memoStats['top_performers'] = MemoAcknowledgment::select('user_id', DB::raw('count(*) as count'))
                ->whereHas('user', function($q) {
                    $q->where('department_id', $this->departmentId);
                })
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->with('user')
                ->get();

            // Recent memos for this department
            $this->memoStats['recent_memos'] = Memo::where(function($q) {
                    $q->where('department_id', $this->departmentId)
                        ->orWhere('audience_type', 'all');
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            $this->memoStats = [
                'by_priority' => collect(),
                'monthly_trend' => collect(),
                'top_performers' => collect(),
                'recent_memos' => collect(),
            ];
        }
    }

    public function loadDocumentStats()
    {
        try {
            $this->documentStats['total'] = Document::where('is_active', true)->count();
            $this->documentStats['by_category'] = Document::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get();
            $this->documentStats['total_downloads'] = Document::sum('download_count');
            $this->documentStats['recent_documents'] = Document::orderBy('created_at', 'desc')->limit(10)->get();
        } catch (\Exception $e) {
            $this->documentStats = [
                'total' => 0,
                'by_category' => collect(),
                'total_downloads' => 0,
                'recent_documents' => collect(),
            ];
        }
    }

    public function loadStaffPerformance()
    {
        try {
            $staff = User::where('department_id', $this->departmentId)
                ->where('is_active', true)
                ->get();

            foreach ($staff as $member) {
                $totalMemos = Memo::where('status', 'published')
                    ->where(function($q) use ($member) {
                        $q->where('audience_type', 'all')
                            ->orWhere('department_id', $this->departmentId);
                    })
                    ->count();

                $acknowledged = MemoAcknowledgment::where('user_id', $member->id)->count();

                $totalDocs = Document::where('is_active', true)->count();
                $docsAcknowledged = DocumentAcknowledgment::where('user_id', $member->id)->count();

                $this->staffPerformance[] = [
                    'user' => $member,
                    'total_memos' => $totalMemos,
                    'acknowledged' => $acknowledged,
                    'memo_rate' => $totalMemos > 0 ? round(($acknowledged / $totalMemos) * 100, 2) : 100,
                    'total_documents' => $totalDocs,
                    'docs_acknowledged' => $docsAcknowledged,
                    'document_rate' => $totalDocs > 0 ? round(($docsAcknowledged / $totalDocs) * 100, 2) : 100,
                    'last_acknowledged' => MemoAcknowledgment::where('user_id', $member->id)
                        ->latest()
                        ->first()?->acknowledged_at,
                ];
            }

            usort($this->staffPerformance, function($a, $b) {
                return $b['memo_rate'] <=> $a['memo_rate'];
            });
        } catch (\Exception $e) {
            $this->staffPerformance = [];
        }
    }

    protected function getDepartmentAcknowledgmentRate()
    {
        try {
            $staffIds = User::where('department_id', $this->departmentId)
                ->where('is_active', true)
                ->pluck('id');

            $totalMemos = Memo::where('status', 'published')
                ->where(function($q) {
                    $q->where('audience_type', 'all')
                        ->orWhere('department_id', $this->departmentId);
                })
                ->count();

            if ($totalMemos === 0) return 100;

            $totalAcknowledgments = MemoAcknowledgment::whereIn('user_id', $staffIds)->count();
            $totalPossible = $totalMemos * count($staffIds);

            if ($totalPossible === 0) return 0;

            return round(($totalAcknowledgments / $totalPossible) * 100, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    // View Memo Audit Trail (only for department staff)
    public function viewMemoAudit($memoId)
    {
        $this->selectedMemo = Memo::find($memoId);
        if (!$this->selectedMemo) return;

        $staffIds = User::where('department_id', $this->departmentId)->pluck('id');

        $auditTrail = [];
        foreach ($staffIds as $staffId) {
            $user = User::find($staffId);
            if ($user) {
                $auditTrail[] = [
                    'user' => $user,
                    'read_at' => DB::table('memo_reads')
                        ->where('memo_id', $memoId)
                        ->where('user_id', $staffId)
                        ->first()?->read_at,
                    'acknowledged_at' => MemoAcknowledgment::where('memo_id', $memoId)
                        ->where('user_id', $staffId)
                        ->first()?->acknowledged_at,
                ];
            }
        }

        $this->memoAuditTrail = $auditTrail;
        $this->showMemoAudit = true;
    }

    public function closeMemoAudit()
    {
        $this->showMemoAudit = false;
        $this->selectedMemo = null;
        $this->memoAuditTrail = [];
    }

    public function exportMemoAuditPDF()
    {
        if (!$this->selectedMemo) {
            session()->flash('error', 'No memo selected for export.');
            return null;
        }

        $data = [
            'memo' => $this->selectedMemo,
            'auditTrail' => $this->memoAuditTrail,
            'departmentName' => $this->departmentName,
            'generated_at' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('exports.hod-memo-audit-pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        $safeMemoNumber = $this->sanitizeFilename($this->selectedMemo->memo_number);
        $safeDeptName = $this->sanitizeFilename($this->departmentName);
        $filename = "memo_audit_{$safeMemoNumber}_{$safeDeptName}.pdf";

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function exportMemoAuditExcel()
    {
        if (!$this->selectedMemo) {
            session()->flash('error', 'No memo selected for export.');
            return null;
        }

        $data = [];
        foreach ($this->memoAuditTrail as $record) {
            $data[] = [
                'Staff Name' => $record['user']->name,
                'Department' => $record['user']->department->name ?? 'N/A',
                'Staff Number' => $record['user']->staff_number,
                'Read Status' => $record['read_at'] ? 'Read' : 'Not Read',
                'Read At' => $record['read_at'] ? \Carbon\Carbon::parse($record['read_at'])->format('Y-m-d h:i A') : '-',
                'Acknowledged' => $record['acknowledged_at'] ? 'Yes' : 'No',
                'Acknowledged At' => $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('Y-m-d h:i A') : '-',
            ];
        }

        $headings = ['Staff Name', 'Department', 'Staff Number', 'Read Status', 'Read At', 'Acknowledged', 'Acknowledged At'];

        $safeMemoNumber = $this->sanitizeFilename($this->selectedMemo->memo_number);
        $safeDeptName = $this->sanitizeFilename($this->departmentName);
        $filename = "memo_audit_{$safeMemoNumber}_{$safeDeptName}.xlsx";

        return Excel::download(new ReportsExport($data, $headings), $filename);
    }

    public function viewDocumentAudit($documentId)
    {
        $this->selectedDocument = Document::find($documentId);
        if (!$this->selectedDocument) return;

        $staffIds = User::where('department_id', $this->departmentId)->pluck('id');

        $auditTrail = [];
        foreach ($staffIds as $staffId) {
            $user = User::find($staffId);
            if ($user) {
                $auditTrail[] = [
                    'user' => $user,
                    'viewed_at' => DocumentView::where('document_id', $documentId)
                        ->where('user_id', $staffId)
                        ->first()?->viewed_at,
                    'acknowledged_at' => DocumentAcknowledgment::where('document_id', $documentId)
                        ->where('user_id', $staffId)
                        ->first()?->acknowledged_at,
                    'downloaded' => DocumentDownload::where('document_id', $documentId)
                        ->where('user_id', $staffId)
                        ->exists(),
                ];
            }
        }

        $this->documentAuditTrail = $auditTrail;
        $this->showDocumentAudit = true;
    }

    public function closeDocumentAudit()
    {
        $this->showDocumentAudit = false;
        $this->selectedDocument = null;
        $this->documentAuditTrail = [];
    }

    public function exportDocumentAuditPDF()
    {
        if (!$this->selectedDocument) {
            session()->flash('error', 'No document selected for export.');
            return null;
        }

        $data = [
            'document' => $this->selectedDocument,
            'auditTrail' => $this->documentAuditTrail,
            'departmentName' => $this->departmentName,
            'generated_at' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('exports.hod-document-audit-pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        $safeDocId = $this->sanitizeFilename($this->selectedDocument->id);
        $safeDeptName = $this->sanitizeFilename($this->departmentName);
        $filename = "document_audit_{$safeDocId}_{$safeDeptName}.pdf";

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function exportDocumentAuditExcel()
    {
        if (!$this->selectedDocument) {
            session()->flash('error', 'No document selected for export.');
            return null;
        }

        $data = [];
        foreach ($this->documentAuditTrail as $record) {
            $data[] = [
                'Staff Name' => $record['user']->name,
                'Department' => $record['user']->department->name ?? 'N/A',
                'Staff Number' => $record['user']->staff_number,
                'Viewed' => $record['viewed_at'] ? 'Yes' : 'No',
                'Viewed At' => $record['viewed_at'] ? \Carbon\Carbon::parse($record['viewed_at'])->format('Y-m-d h:i A') : '-',
                'Acknowledged' => $record['acknowledged_at'] ? 'Yes' : 'No',
                'Acknowledged At' => $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('Y-m-d h:i A') : '-',
                'Downloaded' => $record['downloaded'] ? 'Yes' : 'No',
            ];
        }

        $headings = ['Staff Name', 'Department', 'Staff Number', 'Viewed', 'Viewed At', 'Acknowledged', 'Acknowledged At', 'Downloaded'];

        $safeDocId = $this->sanitizeFilename($this->selectedDocument->id);
        $safeDeptName = $this->sanitizeFilename($this->departmentName);
        $filename = "document_audit_{$safeDocId}_{$safeDeptName}.xlsx";

        return Excel::download(new ReportsExport($data, $headings), $filename);
    }

    public function render()
    {
        return view('livewire.hod.department-reports', [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ])->layout('layouts.app');
    }
}
