<?php

namespace App\Livewire;

use App\Models\Memo;
use App\Models\News;
use App\Models\User;
use Livewire\Component;

class Homepage extends Component
{
    public $unreadMemosCount = 0;
    public $recentNews = [];
    public $pendingMemos = [];
    public $stats = [];

    public function mount()
    {
        $this->loadDashboardData();
    }
    public function loadDashboardData()
    {
       $user = auth()->user();

        // Get unread memos count
        $this->unreadMemosCount = Memo::where('status', 'published')
            ->whereDoesntHave('readBy', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where(function ($query) use ($user) {
    $query->whereJsonContains('recipients', ['type' => 'all'])
        ->orWhereJsonContains('recipients', ['type' => 'department', 'id' => (int)$user->department_id])
        ->orWhereJsonContains('recipients', ['type' => 'user', 'id' => (int)$user->id]);
})
            ->count();

        // Get recent news
        $this->recentNews = News::where('published_at', '<=', now())
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending memos (created by user but not published)
        $this->pendingMemos = Memo::where('created_by', $user->id)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Dashboard statistics
        $this->stats = [
            'total_staff' => User::count(),
            'total_memos' => Memo::where('status', 'published')->count(),
            'total_news' => News::count(),
            'my_memos' => Memo::where('created_by', $user->id)->count(),
        ];
    }
    public function refreshData()
    {
        $this->loadDashboardData();
    }
    public function render()
    {
        return view('livewire.homepage')->layout('layouts.app');
    }
}
