{{-- resources/views/livewire/events/calendar.blade.php --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600">
                <div class="flex justify-between items-center flex-wrap gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('events.index') }}" class="text-white hover:text-purple-200 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to List View
                        </a>
                        <div class="h-6 w-px bg-purple-400"></div>
                        <h1 class="text-2xl font-bold text-white">Event Calendar</h1>
                    </div>

                    @if(auth()->user()->isAdmin() || auth()->user()->isHOD())
                        <a href="{{ route('events.create') }}"
                           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Event
                        </a>
                    @endif
                </div>
            </div>

            <!-- Calendar Navigation -->
            <div class="p-4 border-b flex justify-between items-center flex-wrap gap-3 bg-gray-50">
                <div class="flex gap-2">
                    <button wire:click="previousMonth"
                            class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-100 transition flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous
                    </button>
                    <button wire:click="goToToday"
                            class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-100 transition">
                        Today
                    </button>
                    <button wire:click="nextMonth"
                            class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-100 transition flex items-center gap-1">
                        Next
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <h2 class="text-2xl font-bold text-gray-800">{{ $monthName }}</h2>

                <div class="w-24"></div>
            </div>

            <!-- Legend -->
            <div class="px-4 py-2 bg-gray-50 border-b flex gap-4 text-xs">
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-purple-100 rounded border border-purple-300"></div>
                    <span>Has Events</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-blue-100 rounded border border-blue-300"></div>
                    <span>Today</span>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="p-4 overflow-x-auto">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 gap-2 mb-2">
                    <div class="text-center font-semibold py-2 text-red-500">Sun</div>
                    <div class="text-center font-semibold py-2 text-gray-600">Mon</div>
                    <div class="text-center font-semibold py-2 text-gray-600">Tue</div>
                    <div class="text-center font-semibold py-2 text-gray-600">Wed</div>
                    <div class="text-center font-semibold py-2 text-gray-600">Thu</div>
                    <div class="text-center font-semibold py-2 text-gray-600">Fri</div>
                    <div class="text-center font-semibold py-2 text-blue-500">Sat</div>
                </div>

                <!-- Calendar Days -->
                <div class="space-y-2">
                    @foreach($calendar as $weekIndex => $week)
                        <div class="grid grid-cols-7 gap-2">
                            @foreach($week as $dayIndex => $day)
                                @if($day)
                                    @php
                                        $isToday = $day['isToday'];
                                        $hasEvents = $day['hasEvents'];
                                        $eventCount = $day['eventCount'];
                                    @endphp
                                    <div wire:click="viewDate('{{ $day['date'] }}')"
                                         class="min-h-[120px] p-2 rounded-lg border-2 cursor-pointer transition-all duration-200
                                            {{ $isToday ? 'bg-blue-50 border-blue-400 shadow-md' : '' }}
                                            {{ $hasEvents ? 'bg-purple-50 border-purple-200 hover:border-purple-400' : 'bg-white border-gray-200 hover:border-gray-400' }}
                                            hover:shadow-md">
                                        <div class="flex justify-between items-start">
                                            <span class="font-semibold text-lg
                                                {{ $isToday ? 'text-blue-600' : ($hasEvents ? 'text-purple-600' : 'text-gray-700') }}">
                                                {{ $day['day'] }}
                                            </span>
                                            @if($eventCount > 0)
                                                <span class="bg-purple-500 text-white text-xs rounded-full px-2 py-0.5">
                                                    {{ $eventCount }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($hasEvents)
                                            <div class="mt-2 space-y-1">
                                                @foreach(array_slice($day['events'], 0, 2) as $event)
                                                    <div class="text-xs truncate bg-white rounded px-1 py-0.5 shadow-sm">
                                                        <span class="font-medium">
                                                            @switch($event->type)
                                                                @case('training') 📚 @break
                                                                @case('meeting') 💼 @break
                                                                @case('cme') 🩺 @break
                                                                @case('social') 🎉 @break
                                                                @default 📌
                                                            @endswitch
                                                        </span>
                                                        <span class="ml-1">{{ Str::limit($event->title, 20) }}</span>
                                                    </div>
                                                @endforeach
                                                @if($eventCount > 2)
                                                    <div class="text-xs text-purple-600 text-center">
                                                        +{{ $eventCount - 2 }} more
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="min-h-[120px] p-2 rounded-lg border-2 border-gray-100 bg-gray-50"></div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Events Modal for Selected Date -->
        @if($selectedDate && count($selectedEvents) > 0)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                 x-data="{ open: true }"
                 x-show="open"
                 x-on:click.away="open = false">
                <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[85vh] overflow-hidden">
                    <div class="p-4 border-b bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold">{{ \Carbon\Carbon::parse($selectedDate)->format('l, F d, Y') }}</h3>
                                <p class="text-sm text-purple-200">{{ count($selectedEvents) }} event(s) on this day</p>
                            </div>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200 text-2xl leading-none">
                                ×
                            </button>
                        </div>
                    </div>
                    <div class="p-4 overflow-y-auto max-h-[70vh] space-y-3">
                        @foreach($selectedEvents as $event)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-start justify-between">
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
                                            <h4 class="font-bold text-gray-800">{{ $event->title }}</h4>
                                            <span class="text-xs px-2 py-1 rounded-full
                                                @if($event->type === 'training') bg-blue-100 text-blue-700
                                                @elseif($event->type === 'meeting') bg-green-100 text-green-700
                                                @elseif($event->type === 'cme') bg-yellow-100 text-yellow-700
                                                @elseif($event->type === 'social') bg-red-100 text-red-700
                                                @else bg-gray-100 text-gray-700 @endif">
                                                {{ ucfirst($event->type) }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-2">
                                            <div class="flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>{{ \Carbon\Carbon::parse($event->start_datetime)->format('g:i A') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span>{{ $event->venue }}</span>
                                            </div>
                                        </div>

                                        <p class="text-sm text-gray-600 line-clamp-2">{{ $event->description }}</p>
                                    </div>

                                    <div class="ml-4">
                                        <a href="{{ route('events.show', $event) }}"
                                           class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded-lg text-sm transition">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-3 border-t bg-gray-50 flex justify-end">
                        <button wire:click="closeModal"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
