<?php

namespace App\Livewire\Hod;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Memo;
use App\Models\MemoAcknowledgment;

class DepartmentStaff extends Component
{
    use WithPagination;

    public $search = '';
    public $departmentId;
    public $departmentName;
    public $stats = [];

    public function mount()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if (!$user->department_id) {
            abort(403, 'You are not assigned to any department.');
        }
        
        $this->departmentId = $user->department_id;
        $this->departmentName = $user->department ? $user->department->name : 'Your Department';
        
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_staff' => User::where('department_id', $this->departmentId)
                ->where('is_active', true)
                ->count(),
            'total_memos' => Memo::where('department_id', $this->departmentId)
                ->where('status', 'published')
                ->count(),
            'unacknowledged' => $this->getUnacknowledgedCount(),
            'pending_memos' => Memo::where('department_id', $this->departmentId)
                ->where('status', 'draft')
                ->count(),
        ];
    }

    protected function getUnacknowledgedCount()
    {
        $staffIds = User::where('department_id', $this->departmentId)
            ->where('is_active', true)
            ->pluck('id');
            
        $memos = Memo::where('status', 'published')
            ->where(function($q) {
                $q->where('audience_type', 'all')
                    ->orWhere('department_id', $this->departmentId);
            })
            ->get();
            
        $unacknowledged = 0;
        foreach ($staffIds as $staffId) {
            foreach ($memos as $memo) {
                if (!$memo->acknowledgedBy(User::find($staffId))) {
                    $unacknowledged++;
                }
            }
        }
        
        return $unacknowledged;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // This should return a Paginator instance, not an array
        $staff = User::where('department_id', $this->departmentId)
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('staff_number', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(15);  // This returns a LengthAwarePaginator instance

        // Add computed properties to each staff member
        foreach ($staff as $member) {
            $member->unacknowledged_memos = $this->getStaffUnacknowledgedMemos($member->id);
            $member->acknowledgment_rate = $this->getStaffAcknowledgmentRate($member->id);
        }

        return view('livewire.hod.department-staff', [
            'staff' => $staff,  // This is a Paginator instance, not an array
        ])->layout('layouts.app');
    }

    protected function getStaffUnacknowledgedMemos($userId)
    {
        $user = User::find($userId);
        return $user ? $user->unreadMemos()->count() : 0;
    }

    protected function getStaffAcknowledgmentRate($userId)
    {
        $totalMemos = Memo::where('status', 'published')
            ->where(function($q) use ($userId) {
                $q->where('audience_type', 'all')
                    ->orWhere('department_id', $this->departmentId);
            })
            ->count();
            
        if ($totalMemos === 0) return 100;
        
        $acknowledged = MemoAcknowledgment::where('user_id', $userId)->count();
        return round(($acknowledged / $totalMemos) * 100, 2);
    }
}