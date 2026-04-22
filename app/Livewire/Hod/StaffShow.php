<?php

namespace App\Livewire\Hod;

use Livewire\Component;
use App\Models\User;
use App\Models\Memo;
use App\Models\MemoAcknowledgment;

class StaffShow extends Component
{
    public User $staff;
    public $acknowledgmentHistory = [];
    public $stats = [];

    public function mount(User $staff)
    {
        $user = auth()->user();
        
        // Check if HOD is viewing their own department staff
        if (!$user->isAdmin() && ($user->department_id !== $staff->department_id)) {
            abort(403, 'You can only view staff from your department.');
        }
        
        $this->staff = $staff;
        $this->loadStats();
        $this->loadAcknowledgmentHistory();
    }

    public function loadStats()
    {
        $totalMemos = Memo::where('status', 'published')
            ->where(function($q) {
                $q->where('audience_type', 'all')
                    ->orWhere('department_id', $this->staff->department_id);
            })
            ->count();
            
        $acknowledged = MemoAcknowledgment::where('user_id', $this->staff->id)->count();
        
        $this->stats = [
            'total_memos' => $totalMemos,
            'acknowledged' => $acknowledged,
            'rate' => $totalMemos > 0 ? round(($acknowledged / $totalMemos) * 100, 2) : 100,
            'unacknowledged' => $totalMemos - $acknowledged,
        ];
    }

    public function loadAcknowledgmentHistory()
    {
        $this->acknowledgmentHistory = MemoAcknowledgment::where('user_id', $this->staff->id)
            ->with('memo')
            ->orderBy('acknowledged_at', 'desc')
            ->limit(20)
            ->get();
    }

    public function render()
    {
        return view('livewire.hod.staff-show')->layout('layouts.app');
    }
}