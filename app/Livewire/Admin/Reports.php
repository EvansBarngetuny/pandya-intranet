<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Memo;
use App\Models\News;
use App\Models\Event;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class Reports extends Component
{
    public $reportType = 'staff';
    public $dateRange = 'month';
    
    public function render()
    {
        // Staff Statistics
        $staffStats = [
            'total' => User::count(),
            'by_department' => User::select('department_id', DB::raw('count(*) as count'))
                ->with('department')
                ->groupBy('department_id')
                ->get(),
            'by_role' => User::select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->get(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];
        
        // Memo Statistics
        $memoStats = [
            'total' => Memo::count(),
            'published' => Memo::where('status', 'published')->count(),
            'by_priority' => Memo::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get(),
            'acknowledgment_rate' => $this->getAcknowledgmentRate(),
        ];
        
        // News Statistics
        $newsStats = [
            'total' => News::count(),
            'by_category' => News::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get(),
            'this_month' => News::whereMonth('published_at', now()->month)->count(),
        ];
        
        // Event Statistics
        $eventStats = [
            'total' => Event::count(),
            'upcoming' => Event::where('start_datetime', '>=', now())->count(),
            'by_type' => Event::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get(),
        ];
        
        return view('livewire.admin.reports', [
            'staffStats' => $staffStats,
            'memoStats' => $memoStats,
            'newsStats' => $newsStats,
            'eventStats' => $eventStats,
        ])->layout('layouts.app');
    }
    
    protected function getAcknowledgmentRate()
    {
        $totalMemos = Memo::where('status', 'published')->count();
        if ($totalMemos === 0) return 0;
        
        $totalAcknowledgments = DB::table('memo_acknowledgments')->count();
        $totalPossible = $totalMemos * User::count();
        
        if ($totalPossible === 0) return 0;
        return round(($totalAcknowledgments / $totalPossible) * 100, 2);
    }
}