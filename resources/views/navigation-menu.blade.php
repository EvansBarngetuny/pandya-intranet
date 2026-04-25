{{-- resources/views/layouts/custom-navigation.blade.php --}}
@props(['notifications' => []])

<nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ showNotifications: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    {{-- Your Custom Logo --}}
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}"
                             alt="Pandya Memorial Hospital"
                             class="h-10 w-auto mr-2">
                    @else
                        <span class="text-2xl mr-2">🏥</span>
                    @endif
                    <span class="text-xl font-bold text-gray-800">PICS</span>
                    <span class="ml-2 text-sm text-gray-500 hidden md:block">Pandya Internal Communication System</span>
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
                <div class="relative">
                    <div class="flex items-center space-x-3">
                        <img src="{{ auth()->user()->profile_photo_url }}"
                             class="h-8 w-8 rounded-full object-cover">
                        <div class="hidden md:block">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
