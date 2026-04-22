{{-- resources/views/livewire/events/index.blade.php --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Events & Calendar</h1>
                <p class="text-gray-600 mt-1">Trainings, meetings, and hospital events</p>
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isHOD())
                <a href="{{ route('events.create') }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Event
                </a>
            @endif
        </div>

        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl mb-1">📅</div>
                <div class="text-2xl font-bold text-purple-600">{{ $upcomingCount }}</div>
                <div class="text-xs text-gray-500">Upcoming Events</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl mb-1">📚</div>
                <div class="text-2xl font-bold text-blue-600">{{ $typeStats['training'] }}</div>
                <div class="text-xs text-gray-500">Trainings</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl mb-1">💼</div>
                <div class="text-2xl font-bold text-green-600">{{ $typeStats['meeting'] }}</div>
                <div class="text-xs text-gray-500">Meetings</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl mb-1">🩺</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $typeStats['cme'] }}</div>
                <div class="text-xs text-gray-500">CMEs</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl mb-1">🎉</div>
                <div class="text-2xl font-bold text-red-600">{{ $typeStats['social'] }}</div>
                <div class="text-xs text-gray-500">Social Events</div>
            </div>
        </div>

        <!-- View Toggle & Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <div class="flex flex-wrap justify-between gap-4">
                <div class="flex gap-2">
                    <button wire:click="$set('viewMode', 'list')" 
                            class="px-4 py-2 rounded-lg transition {{ $viewMode === 'list' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        📋 List View
                    </button>
                    <button wire:click="$set('viewMode', 'calendar')" 
                            class="px-4 py-2 rounded-lg transition {{ $viewMode === 'calendar' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        📅 Calendar View
                    </button>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <div class="min-w-[200px]">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Search events..." 
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <select wire:model.live="type" class="rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">All Types</option>
                            <option value="training">📚 Training</option>
                            <option value="meeting">💼 Meeting</option>
                            <option value="cme">🩺 CME</option>
                            <option value="social">🎉 Social</option>
                            <option value="other">📌 Other</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- List View -->
        @if($viewMode === 'list')
            <div class="space-y-4">
                @forelse($events as $event)
                    <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-2xl">
                                        @switch($event->type)
                                            @case('training') 📚 @break
                                            @case('meeting') 💼 @break
                                            @case('cme') 🩺 @break
                                            @case('social') 🎉 @break
                                            @default 📌
                                        @endswitch
                                    </span>
                                    <h3 class="text-xl font-bold text-gray-800">{{ $event->title }}</h3>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ Carbon\Carbon::parse($event->start_datetime)->format('F d, Y') }}</span>
                                        <span>•</span>
                                        <span>{{ Carbon\Carbon::parse($event->start_datetime)->format('h:i A') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>{{ $event->venue }}</span>
                                    </div>
                                </div>
                                
                                <p class="text-gray-600 mt-3">{{ Str::limit($event->description, 150) }}</p>
                                
                                <div class="flex flex-wrap items-center gap-3 mt-4">
                                    <span class="text-xs text-gray-500">
                                        Organized by: {{ $event->organizer->name }}
                                    </span>
                                    @if($event->requires_registration)
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                            Registration Required
                                        </span>
                                        @if($event->max_attendees)
                                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">
                                                {{ $event->registrations()->count() }}/{{ $event->max_attendees }} registered
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('events.show', $event) }}" 
                                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition text-center">
                                    View Details
                                </a>
                                
                                @if($event->requires_registration && !$event->registrations()->where('user_id', auth()->id())->exists())
                                    <button wire:click="registerForEvent({{ $event->id }})"
                                            wire:confirm="Register for this event?"
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                        Register Now
                                    </button>
                                @endif
                                
                                @if($event->registrations()->where('user_id', auth()->id())->exists())
                                    <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm text-center">
                                        ✓ Registered
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <div class="text-6xl mb-4">📅</div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Events Found</h3>
                        <p class="text-gray-500">No upcoming events match your criteria</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($events->hasPages())
                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            @endif
        @endif

        <!-- Calendar View -->
        @if($viewMode === 'calendar')
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📅</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Calendar View Coming Soon</h3>
                    <p class="text-gray-500">Interactive calendar view is under development</p>
                    <button wire:click="$set('viewMode', 'list')" 
                            class="mt-4 bg-purple-600 text-white px-4 py-2 rounded-lg">
                        Switch to List View
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
