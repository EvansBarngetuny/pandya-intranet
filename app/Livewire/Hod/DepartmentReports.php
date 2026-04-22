<?php

namespace App\Livewire\Hod;

use Livewire\Component;
use App\Models\User;
use App\Models\Memo;
use App\Models\MemoAcknowledgment;
use Illuminate\Support\Facades\DB;

class DepartmentReports extends Component
{
    public $departmentId;
    public $departmentName;
    public $dateRange = 'month';
    public $stats = [];
    public $memoStats = [];
    public $staffPerformance = [];

    public function mount()
    {
        $user = auth()->user();
        
        // Debug: Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has department
        if (!$user->department_id) {
            abort(403, 'You are not assigned to any department. Please contact administrator.');
        }
        
        $this->departmentId = $user->department_id;
        $this->departmentName = $user->department ? $user->department->name : 'Your Department';
        
        $this->loadStats();
        $this->loadMemoStats();
        $this->loadStaffPerformance();
    }

    public function loadStats()
    {
        try {
            // Department overview statistics
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
            // Handle error gracefully
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
            // Memo statistics by priority
            $this->memoStats['by_priority'] = Memo::where('department_id', $this->departmentId)
                ->select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get();
                
            // Monthly memo trend
            $this->memoStats['monthly_trend'] = Memo::where('department_id', $this->departmentId)
                ->where('status', 'published')
                ->select(DB::raw('MONTH(published_at) as month'), DB::raw('count(*) as count'))
                ->whereYear('published_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                
            // Top performing staff (most acknowledgments)
            $this->memoStats['top_performers'] = MemoAcknowledgment::select('user_id', DB::raw('count(*) as count'))
                ->whereHas('user', function($q) {
                    $q->where('department_id', $this->departmentId);
                })
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->with('user')
                ->get();
        } catch (\Exception $e) {
            $this->memoStats = [
                'by_priority' => collect(),
                'monthly_trend' => collect(),
                'top_performers' => collect(),
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
                
                $this->staffPerformance[] = [
                    'user' => $member,
                    'total_memos' => $totalMemos,
                    'acknowledged' => $acknowledged,
                    'rate' => $totalMemos > 0 ? round(($acknowledged / $totalMemos) * 100, 2) : 100,
                    'last_acknowledged' => MemoAcknowledgment::where('user_id', $member->id)
                        ->latest()
                        ->first()?->acknowledged_at,
                ];
            }
            
            // Sort by acknowledgment rate
            usort($this->staffPerformance, function($a, $b) {
                return $b['rate'] <=> $a['rate'];
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

    public function render()
    {
        return view('livewire.hod.department-reports', [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ])->layout('layouts.app');
    }
}