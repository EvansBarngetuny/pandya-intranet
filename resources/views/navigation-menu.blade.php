{{-- resources/views/layouts/custom-navigation.blade.php --}}
@props(['notifications' => []])

<nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ showNotifications: false, showUserMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center hover:opacity-80 transition">
                    {{-- Your Custom Logo --}}
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}"
                             alt="Pandya Memorial Hospital"
                             class="h-10 w-auto mr-2">
                    @else
                        <span class="text-2xl mr-2">🏥</span>
                    @endif
                    <span class="ml-2 text-sm text-gray-500 hidden md:block">myPandya</span>
                </a>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Search -->
                <div class="hidden md:block">
                    <div class="relative">
                        <input type="text"
                               id="global-search"
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
                            class="relative p-2 rounded-full hover:bg-gray-100 transition">
                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(isset($notifications) && count($notifications) > 0)
                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                        @endif
                    </button>

                    <div x-show="showNotifications"
                         x-transition
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg overflow-hidden z-50">
                        <div class="p-3 bg-gray-50 border-b flex justify-between items-center">
                            <h3 class="font-semibold text-gray-800">Notifications</h3>
                            @if(isset($notifications) && count($notifications) > 0)
                                <button wire:click="markAllNotificationsRead" class="text-xs text-blue-600 hover:text-blue-800">
                                    Mark all read
                                </button>
                            @endif
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @forelse($notifications ?? [] as $notification)
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
                <div class="relative" x-on:click.away="showUserMenu = false">
                    <button @click="showUserMenu = !showUserMenu"
                            class="flex items-center space-x-3 focus:outline-none hover:opacity-80 transition">
                        <img src="{{ auth()->user()->profile_photo_url }}"
                             class="h-8 w-8 rounded-full object-cover">
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ ucfirst(auth()->user()->role) }}
                                @if(auth()->user()->department)
                                    • {{ auth()->user()->department->name }}
                                @else
                                    • No Dept
                                @endif
                            </p>
                        </div>
                        <svg class="h-4 w-4 text-gray-400 transition-transform"
                             :class="{'rotate-180': showUserMenu}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="showUserMenu"
                         x-transition
                         class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg overflow-hidden z-50 border border-gray-100">

                        <!-- User Info Header -->
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Staff #: {{ auth()->user()->staff_number ?? 'N/A' }}
                            </p>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-1">
                            <!-- Profile Settings (Jetstream) -->
                            <a href="{{ route('profile.show') }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile Settings
                            </a>

                            <!-- API Tokens (if enabled) -->
                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <a href="{{ route('api-tokens.index') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    API Tokens
                                </a>
                            @endif
                        </div>

                        <div class="border-t border-gray-100 my-1"></div>

                        <!-- Admin Links (only visible to Admin) -->
                        @if(auth()->user()->isAdmin())
                            <div class="py-1">
                                <div class="px-4 py-1 text-xs text-gray-400 uppercase">Administration</div>
                                <a href="{{ route('admin.staff.index') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    Staff Management
                                </a>
                                <a href="{{ route('admin.reports') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    System Reports
                                </a>
                                <a href="{{ route('admin.departments') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Departments
                                </a>
                                <a href="{{ route('admin.settings') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    System Settings
                                </a>
                            </div>
                            <div class="border-t border-gray-100 my-1"></div>
                        @endif

                        <!-- HOD Links -->
                        @if(auth()->user()->isHOD())
                            <div class="py-1">
                                <div class="px-4 py-1 text-xs text-gray-400 uppercase">Department</div>
                                <a href="{{ route('hod.staff') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Department Staff
                                </a>
                                <a href="{{ route('hod.reports') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Department Reports
                                </a>
                            </div>
                            <div class="border-t border-gray-100 my-1"></div>
                        @endif

                        <!-- Logout -->
    <div class="py-1">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Logout
        </button>
    </form>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
