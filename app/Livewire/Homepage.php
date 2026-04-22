<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\MemoAcknowledgment;
use App\Models\Memo;
use App\Models\Department;
use App\Models\News;
use App\Models\Event;
use App\Models\Document;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class Homepage extends Component
{
    public $stats = [];
    public $recentNews = [];
    public $upcomingEvents = [];
    public $unreadMemosCount = 0;
    public $recentMemos = [];
    public $notifications = [];
    public $quickActions = [];
    public $showNotificationDropdown = false;

    public function mount()
    {
        $this->loadDashboardData();
        $this->logActivity('viewed_dashboard');
    }

    public function loadDashboardData()
    {
        $user = auth()->user();

        // Unread memos count
        $this->unreadMemosCount = $user->unreadMemos()->count();

        // Recent news (last 5)
        $this->recentNews = News::where('published_at', '<=', now())
            ->where('show_on_homepage', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // Upcoming events (next 30 days)
        $this->upcomingEvents = Event::where('start_datetime', '>=', now())
            ->orderBy('start_datetime', 'asc')
            ->limit(5)
            ->get();

        // Recent memos for the user
        $this->recentMemos = Memo::where('status', 'published')
            ->where(function($q) use ($user) {
                $q->where('audience_type', 'all')
                    ->orWhere(function($q2) use ($user) {
                        $q2->where('audience_type', 'departments')
                            ->whereJsonContains('audience_ids', $user->department_id);
                    })
                    ->orWhere(function($q2) use ($user) {
                        $q2->where('audience_type', 'specific_users')
                            ->whereJsonContains('audience_ids', $user->id);
                    });
            })
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // User notifications
        $this->notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Dashboard stats based on role
        if ($user->isAdmin()) {
            $this->stats = [
                'total_staff' => User::where('is_active', true)->count(),
                'total_departments' => Department::count(),
                'total_memos' => Memo::where('status', 'published')->count(),
                'unacknowledged_memos' => Memo::where('status', 'published')
                    ->whereHas('acknowledgments', null, '<', DB::raw('(SELECT COUNT(*) FROM users)'))
                    ->count(),
                'total_documents' => Document::count(),
                'total_news' => News::count(),
                'total_events' => Event::where('start_datetime', '>=', now())->count(),
            ];
        } elseif ($user->isHOD()) {
            $this->stats = [
                'dept_staff' => User::where('department_id', $user->department_id)
                    ->where('is_active', true)
                    ->count(),
                'dept_memos' => Memo::where('department_id', $user->department_id)
                    ->where('status', 'published')
                    ->count(),
                'pending_acknowledgments' => Memo::where('status', 'published')
                    ->where(function($q) use ($user) {
                        $q->where('audience_type', 'all')
                            ->orWhere('department_id', $user->department_id);
                    })
                    ->whereDoesntHave('acknowledgments', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->count(),
            ];
        } else {
            $this->stats = [
                'my_memos' => Memo::where('status', 'published')
                    ->where(function($q) use ($user) {
                        $q->where('audience_type', 'all')
                            ->orWhere(function($q2) use ($user) {
                                $q2->where('audience_type', 'departments')
                                    ->whereJsonContains('audience_ids', $user->department_id);
                            })
                            ->orWhere(function($q2) use ($user) {
                                $q2->where('audience_type', 'specific_users')
                                    ->whereJsonContains('audience_ids', $user->id);
                            });
                    })
                    ->count(),
                'my_acknowledgments' => MemoAcknowledgment::where('user_id', $user->id)->count(),
                'my_documents' => Document::count(),
            ];
        }

        // Quick actions based on role
        $this->quickActions = $this->getQuickActions();
    }
    protected function getQuickActions()
{
    $user = auth()->user();
    $actions = [];
    
    if ($user->canCreateMemos()) {
        $actions[] = ['name' => 'Create Memo', 'icon' => '📄', 'route' => 'memos.create', 'color' => 'blue'];
    }
    
    if ($user->isAdmin()) {
        $actions[] = ['name' => 'Post News', 'icon' => '📰', 'route' => 'news.create', 'color' => 'green'];
        $actions[] = ['name' => 'Add Staff', 'icon' => '👥', 'route' => 'admin.staff.create', 'color' => 'purple'];
        $actions[] = ['name' => 'Upload Document', 'icon' => '📚', 'route' => 'documents.create', 'color' => 'orange'];
        $actions[] = ['name' => 'Manage Departments', 'icon' => '🏢', 'route' => 'admin.departments', 'color' => 'indigo'];
    }
    
    if ($user->isHOD()) {
        $actions[] = ['name' => 'View Staff', 'icon' => '👥', 'route' => 'hod.staff', 'color' => 'purple'];
        $actions[] = ['name' => 'Dept Reports', 'icon' => '📊', 'route' => 'hod.reports', 'color' => 'orange'];
    }
    
    if ($this->unreadMemosCount > 0) {
        $actions[] = ['name' => 'Acknowledge Memos', 'icon' => '✅', 'route' => 'memos.pending', 'color' => 'red'];
    }
    
    return $actions;
}

// Add a method to get the reports route based on user role
public function getReportsRouteAttribute()
{
    $user = auth()->user();
    if ($user->isAdmin()) {
        return route('admin.reports');
    } elseif ($user->isHOD()) {
        return route('hod.reports');
    }
    return '#';
}
    public function markNotificationRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === auth()->id()) {
            $notification->update(['is_read' => true]);
            $this->notifications = $this->notifications->reject(function($n) use ($notificationId) {
                return $n->id === $notificationId;
            });
            $this->dispatch('notification-read');
        }
    }

    public function markAllNotificationsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        $this->notifications = collect();
        $this->dispatch('all-notifications-read');
    }

    protected function logActivity($action)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => 'dashboard',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function render()
    {
        return view('livewire.homepage')->layout('layouts.app');
    }
}
