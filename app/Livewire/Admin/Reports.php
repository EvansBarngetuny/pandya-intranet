<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Memo;
use App\Models\Document;
use App\Models\MemoRead;
use App\Models\MemoAcknowledgment;
use App\Models\DocumentView;
use App\Models\DocumentAcknowledgment;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class Reports extends Component
{
    use WithPagination;

    // Stats properties
    public $staffStats = [];
    public $memoStats = [];
    public $documentStats = [];

    // Audit modal properties
    public $showMemoAudit = false;
    public $showDocumentAudit = false;
    public $selectedMemo = null;
    public $selectedDocument = null;
    public $memoAuditTrail = [];
    public $documentAuditTrail = [];

    // Filter properties for Memo Audit
    public $memoAuditDepartmentFilter = '';
    public $memoAuditUserFilter = '';
    public $memoAuditStatusFilter = ''; // all, read, unread, acknowledged, pending

    // Filter properties for Document Audit
    public $documentAuditDepartmentFilter = '';
    public $documentAuditUserFilter = '';
    public $documentAuditStatusFilter = ''; // all, viewed, not_viewed, acknowledged, pending

    // Pagination for audit trails
    public $memoAuditPerPage = 10;
    public $documentAuditPerPage = 10;
    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        // Staff Statistics
        $this->staffStats = [
            'total' => User::count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'by_department' => User::with('department')
                ->selectRaw('department_id, count(*) as count')
                ->groupBy('department_id')
                ->get(),
            'by_role' => User::selectRaw('role, count(*) as count')
                ->groupBy('role')
                ->get(),
        ];

        // Memo Statistics
        $this->memoStats = [
            'total' => Memo::count(),
            'published' => Memo::where('status', 'published')->count(),
            'acknowledgment_rate' => $this->calculateOverallAcknowledgmentRate(),
            'by_priority' => Memo::selectRaw('priority, count(*) as count')
                ->groupBy('priority')
                ->get(),
            'recent_memos' => Memo::with('creator')
                ->latest()
                ->limit(5)
                ->get(),
        ];

        // Document Statistics
        $this->documentStats = [
            'total' => Document::count(),
            'total_downloads' => Document::sum('download_count'),
            'by_category' => Document::selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->get(),
            'recent_documents' => Document::latest()->limit(5)->get(),
        ];
    }

    protected function calculateOverallAcknowledgmentRate()
    {
        $totalStaff = User::count();
        if ($totalStaff == 0) return 0;

        $totalAcknowledged = MemoAcknowledgment::distinct('user_id')->count('user_id');
        return round(($totalAcknowledged / $totalStaff) * 100);
    }

    // ==================== MEMO AUDIT METHODS ====================
    
    public function viewMemoAudit($memoId)
    {
        $this->selectedMemo = Memo::with('creator')->find($memoId);
        $this->resetMemoFilters();
        $this->loadMemoAuditTrail();
        $this->showMemoAudit = true;
        $this->resetPage('memoAuditPage');
    }

    public function resetMemoFilters()
    {
        $this->memoAuditDepartmentFilter = '';
        $this->memoAuditUserFilter = '';
        $this->memoAuditStatusFilter = '';
        $this->resetPage('memoAuditPage');
    }

    public function applyMemoAuditFilters()
    {
        $this->resetPage('memoAuditPage');
        $this->loadMemoAuditTrail();
    }

    public function loadMemoAuditTrail()
    {
        if (!$this->selectedMemo) return;

        $query = User::with('department');
        
        if ($this->memoAuditDepartmentFilter) {
            $query->where('department_id', $this->memoAuditDepartmentFilter);
        }
        
        if ($this->memoAuditUserFilter) {
            $query->where('id', $this->memoAuditUserFilter);
        }
        
        $allStaff = $query->get();
        $auditTrail = [];

        foreach ($allStaff as $staff) {
            $readRecord = MemoRead::where('memo_id', $this->selectedMemo->id)
                ->where('user_id', $staff->id)
                ->first();

            $acknowledgment = MemoAcknowledgment::where('memo_id', $this->selectedMemo->id)
                ->where('user_id', $staff->id)
                ->first();

            $record = [
                'user' => $staff,
                'read_at' => $readRecord?->read_at ?? $readRecord?->created_at,
                'acknowledged_at' => $acknowledgment?->acknowledged_at ?? $acknowledgment?->created_at,
            ];
            
            // Apply status filter
            if ($this->memoAuditStatusFilter) {
                $include = false;
                switch ($this->memoAuditStatusFilter) {
                    case 'read':
                        $include = !is_null($record['read_at']);
                        break;
                    case 'unread':
                        $include = is_null($record['read_at']);
                        break;
                    case 'acknowledged':
                        $include = !is_null($record['acknowledged_at']);
                        break;
                    case 'pending':
                        $include = is_null($record['acknowledged_at']);
                        break;
                    default:
                        $include = true;
                }
                
                if ($include) {
                    $auditTrail[] = $record;
                }
            } else {
                $auditTrail[] = $record;
            }
        }

        // Sort by read_at (most recent first)
        usort($auditTrail, function($a, $b) {
            return ($b['read_at'] ?? null) <=> ($a['read_at'] ?? null);
        });

        $this->memoAuditTrail = collect($auditTrail);
    }

    public function getPaginatedMemoAuditProperty()
    {
        if (!$this->selectedMemo) {
            return collect();
        }

        $this->loadMemoAuditTrail();

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('memoAuditPage');
        $currentPageItems = $this->memoAuditTrail->slice(($currentPage - 1) * $this->memoAuditPerPage, $this->memoAuditPerPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $this->memoAuditTrail->count(),
            $this->memoAuditPerPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'memoAuditPage'
            ]
        );
    }

    public function updatingMemoAuditPerPage()
    {
        $this->resetPage('memoAuditPage');
    }

    public function exportMemoAuditPDF()
    {
        if (!$this->selectedMemo) return null;

        try {
            $this->loadMemoAuditTrail();
            $data = [
                'memo' => $this->selectedMemo,
                'auditTrail' => $this->memoAuditTrail,
                'generated_at' => now()->format('F d, Y h:i A'),
                'filters' => [
                    'department' => $this->memoAuditDepartmentFilter,
                    'user' => $this->memoAuditUserFilter,
                    'status' => $this->memoAuditStatusFilter,
                ]
            ];

            $pdf = Pdf::loadView('exports.memo-audit-pdf', $data);
            $pdf->setPaper('A4', 'landscape');

            $safeNumber = $this->sanitizeFilename($this->selectedMemo->memo_number);
            $filename = "memo_audit_{$safeNumber}.pdf";

            return response()->streamDownload(
                fn() => print($pdf->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Exception $e) {
            session()->flash('error', 'PDF generation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function exportMemoAuditExcel()
    {
        if (!$this->selectedMemo) return null;

        $this->loadMemoAuditTrail();
        $exportData = $this->memoAuditTrail->map(function($record) {
            return [
                'Staff Name' => $record['user']->name,
                'Department' => $record['user']->department->name ?? 'N/A',
                'Email' => $record['user']->email,
                'Read Status' => $record['read_at'] ? 'Read' : 'Not Read',
                'Read At' => $record['read_at']?->format('Y-m-d H:i:s'),
                'Acknowledged Status' => $record['acknowledged_at'] ? 'Acknowledged' : 'Pending',
                'Acknowledged At' => $record['acknowledged_at']?->format('Y-m-d H:i:s'),
            ];
        });

        return Excel::download(
            new ReportsExport($exportData, 'Memo Audit Trail - ' . $this->selectedMemo->memo_number),
            'memo_audit_' . $this->sanitizeFilename($this->selectedMemo->memo_number) . '.xlsx'
        );
    }

    // ==================== DOCUMENT AUDIT METHODS ====================
    
    public function viewDocumentAudit($documentId)
    {
        $this->selectedDocument = Document::find($documentId);
        $this->resetDocumentFilters();
        $this->loadDocumentAuditTrail();
        $this->showDocumentAudit = true;
        $this->resetPage('documentAuditPage');
    }

    public function resetDocumentFilters()
    {
        $this->documentAuditDepartmentFilter = '';
        $this->documentAuditUserFilter = '';
        $this->documentAuditStatusFilter = '';
        $this->resetPage('documentAuditPage');
    }

    public function applyDocumentAuditFilters()
    {
        $this->resetPage('documentAuditPage');
        $this->loadDocumentAuditTrail();
    }

    public function loadDocumentAuditTrail()
    {
        if (!$this->selectedDocument) return;

        $query = User::with('department');
        
        if ($this->documentAuditDepartmentFilter) {
            $query->where('department_id', $this->documentAuditDepartmentFilter);
        }
        
        if ($this->documentAuditUserFilter) {
            $query->where('id', $this->documentAuditUserFilter);
        }
        
        $allStaff = $query->get();
        $auditTrail = [];

        foreach ($allStaff as $staff) {
            $viewRecord = DocumentView::where('document_id', $this->selectedDocument->id)
                ->where('user_id', $staff->id)
                ->first();

            $acknowledgment = DocumentAcknowledgment::where('document_id', $this->selectedDocument->id)
                ->where('user_id', $staff->id)
                ->first();

            $record = [
                'user' => $staff,
                'viewed_at' => $viewRecord?->created_at,
                'acknowledged_at' => $acknowledgment?->acknowledged_at ?? $acknowledgment?->created_at,
                'downloaded' => $viewRecord?->download_count > 0 ?? false,
            ];
            
            // Apply status filter for document audit
            if ($this->documentAuditStatusFilter) {
                $include = false;
                switch ($this->documentAuditStatusFilter) {
                    case 'viewed':
                        $include = !is_null($record['viewed_at']);
                        break;
                    case 'not_viewed':
                        $include = is_null($record['viewed_at']);
                        break;
                    case 'acknowledged':
                        $include = !is_null($record['acknowledged_at']);
                        break;
                    case 'pending':
                        $include = is_null($record['acknowledged_at']);
                        break;
                    default:
                        $include = true;
                }
                
                if ($include) {
                    $auditTrail[] = $record;
                }
            } else {
                $auditTrail[] = $record;
            }
        }

        // Sort by viewed_at (most recent first)
        usort($auditTrail, function($a, $b) {
            return ($b['viewed_at'] ?? null) <=> ($a['viewed_at'] ?? null);
        });

        $this->documentAuditTrail = collect($auditTrail);
    }

    public function getPaginatedDocumentAuditProperty()
    {
        if (!$this->selectedDocument) {
            return collect();
        }

        $this->loadDocumentAuditTrail();

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('documentAuditPage');
        $currentPageItems = $this->documentAuditTrail->slice(($currentPage - 1) * $this->documentAuditPerPage, $this->documentAuditPerPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $this->documentAuditTrail->count(),
            $this->documentAuditPerPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'documentAuditPage'
            ]
        );
    }

    public function updatingDocumentAuditPerPage()
    {
        $this->resetPage('documentAuditPage');
    }

    public function exportDocumentAuditPDF()
    {
        if (!$this->selectedDocument) return null;

        try {
            $this->loadDocumentAuditTrail();
            $data = [
                'document' => $this->selectedDocument,
                'auditTrail' => $this->documentAuditTrail,
                'generated_at' => now()->format('F d, Y h:i A'),
                'filters' => [
                    'department' => $this->documentAuditDepartmentFilter,
                    'user' => $this->documentAuditUserFilter,
                    'status' => $this->documentAuditStatusFilter,
                ]
            ];

            $pdf = Pdf::loadView('exports.document-audit-pdf', $data);
            $pdf->setPaper('A4', 'landscape');

            $safeTitle = $this->sanitizeFilename($this->selectedDocument->title);
            $filename = "document_audit_{$safeTitle}.pdf";

            return response()->streamDownload(
                fn() => print($pdf->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Exception $e) {
            session()->flash('error', 'PDF generation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function exportDocumentAuditExcel()
    {
        if (!$this->selectedDocument) return null;

        $this->loadDocumentAuditTrail();
        $exportData = $this->documentAuditTrail->map(function($record) {
            return [
                'Staff Name' => $record['user']->name,
                'Department' => $record['user']->department->name ?? 'N/A',
                'Email' => $record['user']->email,
                'Viewed Status' => $record['viewed_at'] ? 'Viewed' : 'Not Viewed',
                'Viewed At' => $record['viewed_at']?->format('Y-m-d H:i:s'),
                'Acknowledged Status' => $record['acknowledged_at'] ? 'Acknowledged' : 'Pending',
                'Acknowledged At' => $record['acknowledged_at']?->format('Y-m-d H:i:s'),
                'Downloaded' => $record['downloaded'] ? 'Yes' : 'No',
            ];
        });

        return Excel::download(
            new ReportsExport($exportData, 'Document Audit Trail - ' . $this->selectedDocument->title),
            'document_audit_' . $this->sanitizeFilename($this->selectedDocument->title) . '.xlsx'
        );
    }

    // ==================== HELPER METHODS ====================
    
    public function getDepartmentsProperty()
    {
        return Department::orderBy('name')->get();
    }

    public function getMemoAuditUsersProperty()
    {
        $query = User::with('department')->orderBy('name');
        
        if ($this->memoAuditDepartmentFilter) {
            $query->where('department_id', $this->memoAuditDepartmentFilter);
        }
        
        return $query->get();
    }

    public function getDocumentAuditUsersProperty()
    {
        $query = User::with('department')->orderBy('name');
        
        if ($this->documentAuditDepartmentFilter) {
            $query->where('department_id', $this->documentAuditDepartmentFilter);
        }
        
        return $query->get();
    }

    // ==================== CLOSE MODAL METHODS ====================
    
    public function closeMemoAudit()
    {
        $this->showMemoAudit = false;
        $this->selectedMemo = null;
        $this->memoAuditTrail = [];
        $this->resetMemoFilters();
        $this->resetPage('memoAuditPage');
    }

    public function closeDocumentAudit()
    {
        $this->showDocumentAudit = false;
        $this->selectedDocument = null;
        $this->documentAuditTrail = [];
        $this->resetDocumentFilters();
        $this->resetPage('documentAuditPage');
    }

    // ==================== EXPORT REPORT METHODS ====================
    
    public function exportStaffReportPDF()
    {
        $data = [
            'staffStats' => $this->staffStats,
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('exports.staff-report-pdf', $data);
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'staff_report_' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function exportStaffReportExcel()
    {
        $exportData = collect($this->staffStats['by_department'])->map(function($dept) {
            return [
                'Department' => $dept->department->name ?? 'Unassigned',
                'Staff Count' => $dept->count,
            ];
        });

        return Excel::download(
            new ReportsExport($exportData, 'Staff Report'),
            'staff_report_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportMemoReportPDF()
    {
        $data = [
            'memoStats' => $this->memoStats,
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('exports.memo-report-pdf', $data);
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'memo_report_' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function exportMemoReportExcel()
    {
        $exportData = collect($this->memoStats['recent_memos'])->map(function($memo) {
            return [
                'Memo Number' => $memo->memo_number,
                'Title' => $memo->title,
                'Priority' => ucfirst($memo->priority),
                'Status' => ucfirst($memo->status),
                'Created At' => $memo->created_at->format('Y-m-d'),
                'Created By' => $memo->creator->name ?? 'N/A',
            ];
        });

        return Excel::download(
            new ReportsExport($exportData, 'Memo Report'),
            'memo_report_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportDocumentReportPDF()
    {
        $data = [
            'documentStats' => $this->documentStats,
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('exports.document-report-pdf', $data);
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'document_report_' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function exportDocumentReportExcel()
    {
        $exportData = collect($this->documentStats['by_category'])->map(function($category) {
            return [
                'Category' => ucfirst(str_replace('_', ' ', $category->category)),
                'Document Count' => $category->count,
            ];
        });

        return Excel::download(
            new ReportsExport($exportData, 'Document Report'),
            'document_report_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    protected function sanitizeFilename($filename)
    {
        $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $filename);
        $filename = preg_replace('/[^A-Za-z0-9\-_]/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename);
        $filename = trim($filename, '_');
        return substr($filename, 0, 200);
    }

    public function render()
    {
        return view('livewire.admin.reports', [
            'paginatedMemoAudit' => $this->paginatedMemoAudit,
            'paginatedDocumentAudit' => $this->paginatedDocumentAudit,
            'departments' => $this->departments,
            'memoAuditUsers' => $this->memoAuditUsers,
            'documentAuditUsers' => $this->documentAuditUsers,
        ])->layout('layouts.app');
    }
}