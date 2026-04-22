{{-- resources/views/livewire/homepage.blade.php --}}
<div class="min-h-screen bg-gray-50" x-data="{ showNotifications: false }">
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl mr-2">🏥</span>
                        <span class="text-xl font-bold text-gray-800">PICS</span>
                        <span class="ml-2 text-sm text-gray-500">Pandya Internal Communication System</span>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="hidden md:block">
                        <div class="relative">
                            <input type="text" 
                                   placeholder="Search menus..." 
                                   class="w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500">
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="relative" x-on:click.away="showNotifications = false">
                        <button @click="showNotifications = !showNotifications"
                                class="relative p-2 rounded-full hover:bg-gray-100">
                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($notifications->count() > 0)
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                            @endif
                        </button>

                        <div x-show="showNotifications"
                             x-transition
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg overflow-hidden z-50">
                            <div class="p-3 bg-gray-50 border-b flex justify-between items-center">
                                <h3 class="font-semibold text-gray-800">Notifications</h3>
                                @if($notifications->count() > 0)
                                    <button wire:click="markAllNotificationsRead" class="text-xs text-blue-600 hover:text-blue-800">
                                        Mark all read
                                    </button>
                                @endif
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse($notifications as $notification)
                                    <div class="p-3 hover:bg-gray-50 border-b cursor-pointer"
                                         wire:click="markNotificationRead({{ $notification->id }})">
                                        <p class="text-sm font-medium text-gray-800">{{ $notification->title }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $notification->message }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-gray-500">
                                        <p>No new notifications</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="relative">
                        <div class="flex items-center space-x-3">
                            <img src="{{ auth()->user()->profile_photo_url }}"
                                 class="h-8 w-8 rounded-full object-cover">
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ ucfirst(auth()->user()->role) }} • {{ auth()->user()->department->name ?? 'No Dept' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl shadow-lg mb-8">
            <div class="px-8 py-6">
                <div class="flex items-center justify-between flex-wrap">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">
                            Welcome back, {{ auth()->user()->name }}!
                        </h1>
                        <p class="text-blue-100 mt-1">
                            {{ now()->format('l, F j, Y') }} • {{ now()->format('h:i A') }}
                        </p>
                    </div>
                    <div class="mt-3 md:mt-0">
                        <div class="bg-white/20 rounded-full px-4 py-2">
                            <span class="text-white font-medium">
                                @if(auth()->user()->isAdmin())
                                    👑 Administrator
                                @elseif(auth()->user()->isHOD())
                                    👔 Head of {{ auth()->user()->department->name ?? 'Department' }}
                                @else
                                    👤 Staff Member
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @if(auth()->user()->isAdmin())
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Staff</p>
                            <p class="text-2xl font-bold">{{ $stats['total_staff'] ?? 0 }}</p>
                        </div>
                        <div class="text-3xl">👥</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Memos</p>
                            <p class="text-2xl font-bold">{{ $stats['total_memos'] ?? 0 }}</p>
                        </div>
                        <div class="text-3xl">📄</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Departments</p>
                            <p class="text-2xl font-bold">{{ $stats['total_departments'] ?? 0 }}</p>
                        </div>
                        <div class="text-3xl">🏢</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Unread Memos</p>
                            <p class="text-2xl font-bold {{ $unreadMemosCount > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $unreadMemosCount }}
                            </p>
                        </div>
                        <div class="text-3xl">📬</div>
                    </div>
                </div>
            @elseif(auth()->user()->isHOD())
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Department Staff</p>
                            <p class="text-2xl font-bold">{{ $stats['dept_staff'] ?? 0 }}</p>
                        </div>
                        <div class="text-3xl">👥</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Dept Memos</p>
                            <p class="text-2xl font-bold">{{ $stats['dept_memos'] ?? 0 }}</p>
                        </div>
                        <div class="text-3xl">📄</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Pending Acks</p>
                            <p class="text-2xl font-bold">{{ $stats['pending_acknowledgments'] ?? 0 }}</p>
                        </div>
                        <div class="text-3xl">⏳</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Unread Memos</p>
                            <p class="text-2xl font-bold {{ $unreadMemosCount > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $unreadMemosCount }}
                            </p>
                        </div>
                        <div class="text-3xl">📬</div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">My Memos</p>
                            <p class="text-2xl font-bold">{{ $stats['my_memos'] ?? 0 }}</p>
                        </div>
                        <div class="text-3xl">📄</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Acknowledged</p>
                            <p class="text-2xl font-bold">{{ $stats['my_acknowledgments'] ?? 0 }}</p>
                        </div>
                        <div class="text-3xl">✅</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Documents</p>
                            <p class="text-2xl font-bold">{{ $stats['my_documents'] ?? 0 }}</p>
                        </div>
                        <div class="text-3xl">📚</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Unread Memos</p>
                            <p class="text-2xl font-bold {{ $unreadMemosCount > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $unreadMemosCount }}
                            </p>
                        </div>
                        <div class="text-3xl">📬</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Actions Row - 5 columns -->
        @if(count($quickActions) > 0)
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Quick Actions</h2>
            <div class="grid grid-cols-5 gap-4">
                @foreach($quickActions as $action)
                <a href="{{ route($action['route']) }}" class="group">
                    <div class="bg-white rounded-xl shadow p-4 text-center hover:shadow-lg transition group">
                        <div class="text-3xl mb-2">{{ $action['icon'] }}</div>
                        <p class="text-sm font-medium text-gray-700 group-hover:text-blue-600">
                            {{ $action['name'] }}
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Applications Section - Proper 5 Columns Per Row -->
        <div class="mb-10">
    <h2 class="text-xl font-semibold text-gray-800 mb-6 text-center pb-2 border-b">
        📱 Applications
    </h2>
    
    <!-- ROW 1: Core Applications -->
    <div class="flex justify-between gap-4 mb-6 w-full">
        <!-- News -->
        <div class="flex-1">
            <a href="{{ route('news.index') }}" class="group block">
                <div class="app-tile">
                    <div class="icon">📰</div>
                    <div class="label">News</div>
                </div>
            </a>
        </div>

        <!-- Memos -->
        <div class="flex-1">
            <a href="{{ route('memos.index') }}" class="group relative block">
                <div class="app-tile">
                    <div class="icon">📄</div>
                    <div class="label">Memos</div>
                    @if($unreadMemosCount > 0)
                        <span class="badge">{{ $unreadMemosCount }}</span>
                    @endif
                </div>
            </a>
        </div>

        <!-- Events -->
        <div class="flex-1">
            <a href="{{ route('events.index') }}" class="group block">
                <div class="app-tile">
                    <div class="icon">📅</div>
                    <div class="label">Events</div>
                </div>
            </a>
        </div>

        <!-- Policies/Documents -->
        <div class="flex-1">
            <a href="{{ route('documents.index') }}" class="group block">
                <div class="app-tile">
                    <div class="icon">📚</div>
                    <div class="label">Policies</div>
                </div>
            </a>
        </div>

        <!-- Acknowledge Memos -->
        <div class="flex-1">
            <a href="{{ route('memos.pending') }}" class="group relative block">
                <div class="app-tile">
                    <div class="icon">✅</div>
                    <div class="label">Acknowledge</div>
                    @if($unreadMemosCount > 0)
                        <span class="badge">{{ $unreadMemosCount }}</span>
                    @endif
                </div>
            </a>
        </div>
    </div>

    <!-- ROW 2: User Related Applications -->
    <div class="flex justify-between gap-4 mb-6 w-full">
        <!-- My Profile -->
        <div class="flex-1">
            <a href="{{ route('profile.show') }}" class="group block">
                <div class="app-tile">
                    <div class="icon">👤</div>
                    <div class="label">My Profile</div>
                </div>
            </a>
        </div>

        @if(auth()->user()->canViewReports())
        <div class="flex-1">
            <a href="{{ auth()->user()->isAdmin() ? route('admin.reports') : route('hod.reports') }}" class="group block">
                <div class="app-tile">
                    <div class="icon">📊</div>
                    <div class="label">Reports</div>
                </div>
            </a>
        </div>
        @endif

        @if(auth()->user()->canManageStaff())
        <div class="flex-1">
            <a href="{{ route('admin.staff.index') }}" class="group block">
                <div class="app-tile">
                    <div class="icon">👥</div>
                    <div class="label">Staff</div>
                </div>
            </a>
        </div>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="flex-1">
            <a href="{{ route('admin.departments') }}" class="group block">
                <div class="app-tile">
                    <div class="icon">🏢</div>
                    <div class="label">Departments</div>
                </div>
            </a>
        </div>
        <div class="flex-1">
            <a href="{{ route('admin.settings') }}" class="group block">
                <div class="app-tile">
                    <div class="icon">⚙️</div>
                    <div class="label">Settings</div>
                </div>
            </a>
        </div>
        @elseif(auth()->user()->isHOD())
        <div class="flex-1">
            <a href="{{ route('hod.staff') }}" class="group block">
                <div class="app-tile">
                    <div class="icon">👥</div>
                    <div class="label">Dept Staff</div>
                </div>
            </a>
        </div>
        <div class="flex-1">
            <a href="{{ route('hod.reports') }}" class="group block">
                <div class="app-tile">
                    <div class="icon">📈</div>
                    <div class="label">Dept Reports</div>
                </div>
            </a>
        </div>
        @endif
    </div>

    <!-- ROW 3: Admin Only Applications -->
    @if(auth()->user()->isAdmin())
    <div class="flex justify-between gap-4 mb-6 w-full">
        <div class="flex-1">
            <a href="#" class="group block">
                <div class="app-tile">
                    <div class="icon">🔐</div>
                    <div class="label">Audit Logs</div>
                </div>
            </a>
        </div>
        <div class="flex-1">
            <a href="#" class="group block">
                <div class="app-tile">
                    <div class="icon">💾</div>
                    <div class="label">Backup</div>
                </div>
            </a>
        </div>
        <div class="flex-1">
            <a href="#" class="group block">
                <div class="app-tile">
                    <div class="icon">📧</div>
                    <div class="label">Email Logs</div>
                </div>
            </a>
        </div>
        <div class="flex-1">
            <a href="#" class="group block">
                <div class="app-tile">
                    <div class="icon">🔄</div>
                    <div class="label">System Sync</div>
                </div>
            </a>
        </div>
        <div class="flex-1">
            <a href="#" class="group block">
                <div class="app-tile">
                    <div class="icon">📊</div>
                    <div class="label">Analytics</div>
                </div>
            </a>
        </div>
    </div>
    @endif

    <!-- ROW 3 for HOD -->
    @if(auth()->user()->isHOD())
    <div class="flex justify-between gap-4 mb-6 w-full">
        <div class="flex-1">
            <a href="#" class="group block">
                <div class="app-tile">
                    <div class="icon">📋</div>
                    <div class="label">Staff Leave</div>
                </div>
            </a>
        </div>
        <div class="flex-1">
            <a href="#" class="group block">
                <div class="app-tile">
                    <div class="icon">⏰</div>
                    <div class="label">Attendance</div>
                </div>
            </a>
        </div>
        <div class="flex-1">
            <a href="#" class="group block">
                <div class="app-tile">
                    <div class="icon">📝</div>
                    <div class="label">Evaluations</div>
                </div>
            </a>
        </div>
        <div class="flex-1">
            <a href="#" class="group block">
                <div class="app-tile">
                    <div class="icon">📅</div>
                    <div class="label">Schedule</div>
                </div>
            </a>
        </div>
        <div class="flex-1">
            <a href="#" class="group block">
                <div class="app-tile">
                    <div class="icon">📢</div>
                    <div class="label">Announcements</div>
                </div>
            </a>
        </div>
    </div>
    @endif   
</div>
        <!-- Recent Updates Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10">
            <!-- Recent News -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <span class="text-2xl mr-2">📰</span> Latest News
                    </h2>
                    <a href="{{ route('news.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all →
                    </a>
                </div>
                <div class="divide-y divide-gray-200 max-h-[400px] overflow-y-auto">
                    @forelse($recentNews as $news)
                    <div class="p-4 hover:bg-gray-50 transition cursor-pointer"
                         onclick="window.location='{{ route('news.show', $news) }}'">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 text-2xl">
                                @switch($news->category)
                                    @case('announcement') 📢 @break
                                    @case('achievement') 🏆 @break
                                    @case('facility') 🏥 @break
                                    @default 📰
                                @endswitch
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    @if($news->is_pinned)
                                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">📌 Pinned</span>
                                    @endif
                                    <span class="text-xs text-gray-500">{{ $news->published_at->diffForHumans() }}</span>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm mb-1">{{ $news->title }}</h3>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $news->summary }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        <p>No news available</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <span class="text-2xl mr-2">📅</span> Upcoming Events
                    </h2>
                    <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all →
                    </a>
                </div>
                <div class="divide-y divide-gray-200 max-h-[400px] overflow-y-auto">
                    @forelse($upcomingEvents as $event)
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 text-center min-w-[50px]">
                                <div class="bg-blue-100 rounded-lg px-2 py-1">
                                    <div class="text-lg font-bold text-blue-600">{{ $event->start_datetime->format('d') }}</div>
                                    <div class="text-xs text-blue-500">{{ $event->start_datetime->format('M') }}</div>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 text-sm">{{ $event->title }}</h3>
                                <p class="text-xs text-gray-600 mt-1">{{ $event->venue }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-xs text-gray-500">🕒 {{ $event->start_datetime->format('h:i A') }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        @if($event->type === 'training') bg-purple-100 text-purple-700
                                        @elseif($event->type === 'meeting') bg-blue-100 text-blue-700
                                        @elseif($event->type === 'cme') bg-green-100 text-green-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ strtoupper($event->type) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        <p>No upcoming events</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Memos for HOD/Admin -->
        @if(auth()->user()->isAdmin() || auth()->user()->isHOD())
        <div class="mt-8 bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <span class="text-2xl mr-2">📄</span> Recent Memos
                </h2>
                <a href="{{ route('memos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                    + Create New Memo
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Memo #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Published</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acknowledged</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentMemos as $memo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $memo->memo_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $memo->title }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($memo->priority === 'urgent') bg-red-100 text-red-800
                                    @elseif($memo->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($memo->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($memo->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $memo->published_at?->diffForHumans() }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full" style="width: {{ $memo->acknowledgment_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600 min-w-[40px]">{{ $memo->acknowledgment_percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('memos.show', $memo) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No memos found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">© {{ date('Y') }} Pandya Memorial Hospital</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-sm text-gray-500">PICS v1.0</span>
                </div>
                <div class="flex space-x-4 mt-2 md:mt-0">
                    <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Help</a>
                    <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Privacy</a>
                    <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Contact IT</a>
                </div>
            </div>
        </div>
    </footer>
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
  .app-tile {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    background: white;
    border-radius: 16px;
    padding: 20px 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    transition: all 0.25s ease;
    text-align: center;
    cursor: pointer;
    border: 1px solid #e5e7eb;
    min-height: 110px;
    width: 100%; /* Ensure tile takes full width of its container */
}

.app-tile:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: #3b82f6;
    background: #f8fafc;
}

.icon {
    font-size: 36px;
    margin-bottom: 10px;
    transition: transform 0.2s ease;
}

.group:hover .icon {
    transform: scale(1.1);
}

.label {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    text-align: center;
}

.badge {
    position: absolute;
    top: 8px;
    right: 12px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 9999px;
    min-width: 20px;
    text-align: center;
}

/* Ensure grid containers take full width */
.grid-cols-5 {
    grid-template-columns: repeat(5, minmax(0, 1fr));
    width: 100%;
}

/* Make sure the parent container doesn't restrict width */
.max-w-7xl {
    max-width: 80rem;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .app-tile {
        padding: 12px 8px;
        min-height: 90px;
    }
    .icon {
        font-size: 28px;
    }
    .label {
        font-size: 10px;
    }
}

/* For smaller screens, reduce columns */
@media (max-width: 640px) {
    .grid-cols-5 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}
</style>
@endpush