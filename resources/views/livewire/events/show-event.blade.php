{{-- resources/views/livewire/events/show-event.blade.php --}}
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600">
                <div class="flex justify-between items-center">
                    <a href="{{ route('events.index') }}" class="text-white hover:text-purple-200 flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Events
                    </a>
                    
                    @if(auth()->user()->isAdmin() || auth()->user()->id === $event->organizer_id)
                        <div class="flex gap-2">
                            <a href="{{ route('events.edit', $event) }}" 
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                Edit Event
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="p-6 md:p-8">
                <!-- Event Type & Title -->
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-4xl">
                        @switch($event->type)
                            @case('training') 📚 @break
                            @case('meeting') 💼 @break
                            @case('cme') 🩺 @break
                            @case('social') 🎉 @break
                            @default 📌
                        @endswitch
                    </span>
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full text-sm
                            @if($event->type === 'training') bg-blue-100 text-blue-700
                            @elseif($event->type === 'meeting') bg-green-100 text-green-700
                            @elseif($event->type === 'cme') bg-yellow-100 text-yellow-700
                            @elseif($event->type === 'social') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ strtoupper($event->type) }}
                        </span>
                    </div>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $event->title }}</h1>
                
                <!-- Event Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Date & Time</p>
                            <p class="font-medium">{{ Carbon\Carbon::parse($event->start_datetime)->format('F d, Y') }}</p>
                            <p class="text-sm">{{ Carbon\Carbon::parse($event->start_datetime)->format('h:i A') }} - {{ Carbon\Carbon::parse($event->end_datetime)->format('h:i A') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Venue</p>
                            <p class="font-medium">{{ $event->venue }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Organized By</p>
                            <p class="font-medium">{{ $event->organizer->name }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Contact</p>
                            <p class="font-medium">{{ $event->contact_person ?? 'N/A' }}</p>
                            @if($event->contact_phone)
                                <p class="text-sm">{{ $event->contact_phone }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">About This Event</h3>
                    <div class="prose max-w-none">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>
                
                <!-- Target Departments -->
                @if($event->target_departments && count($event->target_departments) > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Target Audience</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($event->target_departments as $dept)
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                    {{ $dept }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Registration Section -->
                @if($event->requires_registration)
                    <div class="border-t pt-6 mt-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex flex-wrap justify-between items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Registration</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        @if($event->max_attendees)
                                            {{ $attendeeCount }} / {{ $event->max_attendees }} spots filled
                                            <div class="w-48 h-2 bg-gray-200 rounded-full mt-2">
                                                <div class="h-2 bg-green-500 rounded-full" style="width: {{ ($attendeeCount / $event->max_attendees) * 100 }}%"></div>
                                            </div>
                                        @else
                                            Open registration
                                        @endif
                                    </p>
                                </div>
                                
                                <div>
                                    @if(!$isRegistered && (!$event->max_attendees || $attendeeCount < $event->max_attendees))
                                        <button wire:click="register"
                                                wire:confirm="Register for {{ $event->title }}?"
                                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                                            Register for Event
                                        </button>
                                    @elseif($isRegistered)
                                        <div class="text-center">
                                            <span class="inline-block bg-green-100 text-green-700 px-4 py-2 rounded-lg">
                                                ✓ You are registered
                                            </span>
                                            <button wire:click="cancelRegistration"
                                                    wire:confirm="Cancel your registration?"
                                                    class="block mt-2 text-red-600 hover:text-red-800 text-sm">
                                                Cancel Registration
                                            </button>
                                        </div>
                                    @else
                                        <span class="inline-block bg-red-100 text-red-700 px-4 py-2 rounded-lg">
                                            Fully Booked
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>