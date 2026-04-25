{{-- resources/views/livewire/homepage.blade.php --}}
<div class="min-h-screen bg-gray-50" x-data="{ showNotifications: false }">
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
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
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

        <!-- Applications Section -->
        <div class="mb-10">
            <h2 class="text-xl font-semibold text-gray-800 mb-6 text-center pb-2 border-b">
                📱 Applications
            </h2>

            <!-- ROW 1: Core Applications -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-6">
                <!-- News -->
                <a href="{{ route('news.index') }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📰</div>
                        <h3 class="font-semibold text-gray-800 text-sm">News</h3>
                        <p class="text-xs text-gray-500 mt-1">Announcements</p>
                    </div>
                </a>

                <!-- Memos -->
                <a href="{{ route('memos.index') }}" class="block relative">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📄</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Memos</h3>
                        <p class="text-xs text-gray-500 mt-1">Official</p>
                        @if($unreadMemosCount > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $unreadMemosCount }}
                            </span>
                        @endif
                    </div>
                </a>

                <!-- Events -->
                <a href="{{ route('events.index') }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📅</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Events</h3>
                        <p class="text-xs text-gray-500 mt-1">Trainings</p>
                    </div>
                </a>

                <!-- Policies/Documents -->
                <a href="{{ route('documents.index') }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📚</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Policies</h3>
                        <p class="text-xs text-gray-500 mt-1">SOPs & Forms</p>
                    </div>
                </a>

                <!-- Acknowledge Memos -->
                <a href="{{ route('memos.pending') }}" class="block relative">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">✅</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Acknowledge</h3>
                        <p class="text-xs text-gray-500 mt-1">Mark as read</p>
                        @if($unreadMemosCount > 0)
                            <span class="absolute -top-2 -right-2 bg-yellow-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $unreadMemosCount }}
                            </span>
                        @endif
                    </div>
                </a>
            </div>

            <!-- ROW 2: User Related Applications -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-6">
                <!-- My Profile -->
                <a href="{{ route('profile.show') }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">👤</div>
                        <h3 class="font-semibold text-gray-800 text-sm">My Profile</h3>
                        <p class="text-xs text-gray-500 mt-1">View info</p>
                    </div>
                </a>

                @if(auth()->user()->canViewReports())
                <a href="{{ auth()->user()->isAdmin() ? route('admin.reports') : route('hod.reports') }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📊</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Reports</h3>
                        <p class="text-xs text-gray-500 mt-1">Analytics</p>
                    </div>
                </a>
                @endif

                @if(auth()->user()->canManageStaff())
                <a href="{{ route('admin.staff.index') }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">👥</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Staff</h3>
                        <p class="text-xs text-gray-500 mt-1">Directory</p>
                    </div>
                </a>
                @endif

                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.departments') }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">🏢</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Departments</h3>
                        <p class="text-xs text-gray-500 mt-1">Manage</p>
                    </div>
                </a>

                <a href="{{ route('admin.settings') }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">⚙️</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Settings</h3>
                        <p class="text-xs text-gray-500 mt-1">Configure</p>
                    </div>
                </a>
                @elseif(auth()->user()->isHOD())
                <a href="{{ route('hod.staff') }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">👥</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Dept Staff</h3>
                        <p class="text-xs text-gray-500 mt-1">My Team</p>
                    </div>
                </a>

                <a href="{{ route('hod.reports') }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📈</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Dept Reports</h3>
                        <p class="text-xs text-gray-500 mt-1">Analytics</p>
                    </div>
                </a>
                @endif
            </div>

            <!-- ROW 3: Admin Only Applications -->
            @if(auth()->user()->isAdmin())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                <a href="#" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">🔐</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Audit Logs</h3>
                        <p class="text-xs text-gray-500 mt-1">Security</p>
                    </div>
                </a>
                <a href="#" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">💾</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Backup</h3>
                        <p class="text-xs text-gray-500 mt-1">Database</p>
                    </div>
                </a>
                <a href="#" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📧</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Email Logs</h3>
                        <p class="text-xs text-gray-500 mt-1">History</p>
                    </div>
                </a>
                <a href="#" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">🔄</div>
                        <h3 class="font-semibold text-gray-800 text-sm">System Sync</h3>
                        <p class="text-xs text-gray-500 mt-1">Integration</p>
                    </div>
                </a>
                <a href="#" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📊</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Analytics</h3>
                        <p class="text-xs text-gray-500 mt-1">Insights</p>
                    </div>
                </a>
            </div>
            @endif

            <!-- ROW 3 for HOD -->
            @if(auth()->user()->isHOD())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                <a href="#" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📋</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Staff Leave</h3>
                        <p class="text-xs text-gray-500 mt-1">Requests</p>
                    </div>
                </a>
                <a href="#" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">⏰</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Attendance</h3>
                        <p class="text-xs text-gray-500 mt-1">Tracking</p>
                    </div>
                </a>
                <a href="#" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📝</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Evaluations</h3>
                        <p class="text-xs text-gray-500 mt-1">Performance</p>
                    </div>
                </a>
                <a href="#" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📅</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Schedule</h3>
                        <p class="text-xs text-gray-500 mt-1">Roster</p>
                    </div>
                </a>
                <a href="#" class="block">
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 text-center border border-gray-100 hover:border-blue-500 group">
                        <div class="text-4xl mb-2 group-hover:scale-110 transition-transform">📢</div>
                        <h3 class="font-semibold text-gray-800 text-sm">Announcements</h3>
                        <p class="text-xs text-gray-500 mt-1">Updates</p>
                    </div>
                </a>
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

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .grid-cols-5 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
</style>
@endpush