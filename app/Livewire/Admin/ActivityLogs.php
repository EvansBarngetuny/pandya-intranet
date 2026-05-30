<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActivityLogsExport;

class ActivityLogs extends Component
{
    use WithPagination;

    // Filter properties
    public $search = '';
    public $module = '';
    public $action = '';
    public $user_id = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    // Sorting properties
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Modal properties
    public $showDetailsModal = false;
    public $selectedLogProperties = [];

    // Stats properties
    public $totalLogs = 0;
    public $totalUsers = 0;
    public $todayActivities = 0;
    public $uniqueIps = 0;

    // Export properties
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->updateStats();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingModule()
    {
        $this->resetPage();
    }

    public function updatingAction()
    {
        $this->resetPage();
    }

    public function updatingUserId()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'module', 'action', 'user_id', 'date_from', 'date_to']);
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
        $this->updateStats();
    }

    public function viewDetails($logId)
    {
        $log = ActivityLog::findOrFail($logId);
        $this->selectedLogProperties = $log->properties ?? [];
        $this->showDetailsModal = true;
    }

    public function closeModal()
    {
        $this->showDetailsModal = false;
        $this->selectedLogProperties = [];
    }

    public function exportPDF()
    {
        $logs = $this->getExportData();

        $pdf = Pdf::loadView('exports.activity-logs-pdf', [
            'logs' => $logs,
            'filters' => [
                'search' => $this->search,
                'module' => $this->module,
                'action' => $this->action,
                'user_id' => $this->user_id,
                'date_from' => $this->date_from,
                'date_to' => $this->date_to,
            ],
            'exported_at' => now(),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'activity-logs-' . now()->format('Y-m-d-His') . '.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new ActivityLogsExport($this->getExportData()),
            'activity-logs-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    protected function getExportData()
    {
        return ActivityLog::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                      ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%')
                                    ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->module, fn($query) => $query->where('module', $this->module))
            ->when($this->action, fn($query) => $query->where('action', $this->action))
            ->when($this->user_id, fn($query) => $query->where('user_id', $this->user_id))
            ->when($this->date_from, fn($query) => $query->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn($query) => $query->whereDate('created_at', '<=', $this->date_to))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
    }

    protected function updateStats()
    {
        $query = ActivityLog::query();

        // Apply current filters to stats
        $query->when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('description', 'like', '%' . $this->search . '%')
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%');
            });
        })
        ->when($this->module, fn($q) => $q->where('module', $this->module))
        ->when($this->action, fn($q) => $q->where('action', $this->action))
        ->when($this->user_id, fn($q) => $q->where('user_id', $this->user_id))
        ->when($this->date_from, fn($q) => $q->whereDate('created_at', '>=', $this->date_from))
        ->when($this->date_to, fn($q) => $q->whereDate('created_at', '<=', $this->date_to));

        $this->totalLogs = $query->count();
        $this->todayActivities = $query->whereDate('created_at', today())->count();
        $this->uniqueIps = $query->distinct('ip_address')->count('ip_address');
        $this->totalUsers = User::count();
    }

    public function getModulesProperty()
    {
        return ActivityLog::distinct()->pluck('module')->filter()->values();
    }

    public function getActionsProperty()
    {
        return ActivityLog::distinct()->pluck('action')->filter()->values();
    }

    public function getUsersProperty()
    {
        return User::orderBy('name')->get();
    }

    public function getLogsProperty()
    {
        return ActivityLog::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                      ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%')
                                    ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->module, fn($query) => $query->where('module', $this->module))
            ->when($this->action, fn($query) => $query->where('action', $this->action))
            ->when($this->user_id, fn($query) => $query->where('user_id', $this->user_id))
            ->when($this->date_from, fn($query) => $query->whereDate('created_at', '>=', $this->date_from))
            ->when($this->date_to, fn($query) => $query->whereDate('created_at', '<=', $this->date_to))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.activity-logs', [
            'logs' => $this->logs,
            'modules' => $this->modules,
            'actions' => $this->actions,
            'users' => $this->users,
            'totalLogs' => $this->totalLogs,
            'totalUsers' => $this->totalUsers,
            'todayActivities' => $this->todayActivities,
            'uniqueIps' => $this->uniqueIps,
        ])->layout('layouts.app');
    }
}
