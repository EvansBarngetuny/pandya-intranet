{{-- resources/views/livewire/events/calendar.blade.php --}}
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600">
                <div class="flex justify-between items-center">
                    <a href="{{ route('events.index') }}" class="text-white hover:text-purple-200 flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to List View
                    </a>
                    <h1 class="text-2xl font-bold text-white">Event Calendar</h1>
                    <div class="w-20"></div>
                </div>
            </div>
            
            <!-- Calendar Navigation -->
            <div class="p-4 border-b flex justify-between items-center">
                <button wire:click="previousMonth" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    ← Previous
                </button>
                <h2 class="text-xl font-bold text-gray-800">{{ $monthName }}</h2>
                <button wire:click="nextMonth" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Next →
                </button>
            </div>
            
            <!-- Calendar Grid -->
            <div class="p-4">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 gap-2 mb-2">
                    <div class="text-center font-bold text-red-500">Sun</div>
                    <div class="text-center font-bold">Mon</div>
                    <div class="text-center font-bold">Tue</div>
                    <div class="text-center font-bold">Wed</div>
                    <div class="text-center font-bold">Thu</div>
                    <div class="text-center font-bold">Fri</div>
                    <div class="text-center font-bold text-blue-500">Sat</div>
                </div>
                
                <!-- Calendar Days -->
                @foreach($calendar as $week)
                    <div class="grid grid-cols-7 gap-2 mb-2">
                        @foreach($week as $day)
                            @if($day)
                                <div wire:click="viewDate('{{ $day['date'] }}')" 
                                     class="min-h-[100px] p-2 border rounded-lg cursor-pointer hover:shadow-md transition
                                        {{ $day['hasEvents'] ? 'bg-purple-50 border-purple-300' : 'bg-white' }}">
                                    <div class="font-semibold {{ $day['hasEvents'] ? 'text-purple-600' : 'text-gray-700' }}">
                                        {{ $day['day'] }}
                                    </div>
                                    @if($day['hasEvents'])
                                        <div class="mt-1">
                                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                            <span class="text-xs text-gray-600">{{ $day['events']->count() }} event(s)</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="min-h-[100px] p-2 border rounded-lg bg-gray-50"></div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Events Modal for Selected Date -->
        @if($selectedDate && $selectedEvents->count() > 0)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="viewDate(null)">
                <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto">
                    <div class="p-4 border-b bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-t-lg">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold">Events for {{ Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</h3>
                            <button wire:click="viewDate(null)" class="text-white hover:text-gray-200">×</button>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        @foreach($selectedEvents as $event)
                            <div class="border rounded-lg p-3 hover:shadow-md transition">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xl">
                                        @switch($event->type)
                                            @case('training') 📚 @break
                                            @case('meeting') 💼 @break
                                            @case('cme') 🩺 @break
                                            @case('social') 🎉 @break
                                            @default 📌
                                        @endswitch
                                    </span>
                                    <h4 class="font-semibold text-gray-800">{{ $event->title }}</h4>
                                </div>
                                <p class="text-sm text-gray-600">{{ $event->venue }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ Carbon\Carbon::parse($event->start_datetime)->format('h:i A') }}</p>
                                <a href="{{ route('events.show', $event) }}" class="mt-2 inline-block text-purple-600 hover:text-purple-800 text-sm">
                                    View Details →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>