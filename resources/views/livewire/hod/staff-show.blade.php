{{-- resources/views/livewire/hod/staff-show.blade.php --}}
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800">
                <div class="flex justify-between items-center">
                    <a href="{{ route('hod.staff') }}" class="text-white hover:text-blue-200 flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Staff List
                    </a>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Staff Profile -->
                <div class="flex items-center space-x-6 mb-6 pb-6 border-b">
                    <img src="{{ $staff->profile_photo_url }}" class="h-24 w-24 rounded-full object-cover">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $staff->name }}</h1>
                        <p class="text-gray-600">{{ $staff->position ?? 'Staff Member' }}</p>
                        <p class="text-sm text-gray-500">Staff #: {{ $staff->staff_number }}</p>
                        <p class="text-sm text-gray-500">Email: {{ $staff->email }}</p>
                        <p class="text-sm text-gray-500">Phone: {{ $staff->phone ?? 'Not provided' }}</p>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['total_memos'] }}</div>
                        <div class="text-xs text-gray-500">Total Memos</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['acknowledged'] }}</div>
                        <div class="text-xs text-gray-500">Acknowledged</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $stats['unacknowledged'] }}</div>
                        <div class="text-xs text-gray-500">Unacknowledged</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $stats['rate'] }}%</div>
                        <div class="text-xs text-gray-500">Acknowledgment Rate</div>
                    </div>
                </div>
                
                <!-- Acknowledgment History -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Acknowledgment History</h2>
                    <div class="space-y-3">
                        @forelse($acknowledgmentHistory as $ack)
                            <div class="border rounded-lg p-3 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-gray-800">{{ $ack->memo->title }}</h3>
                                        <p class="text-xs text-gray-500">Memo #: {{ $ack->memo->memo_number }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-green-600">Acknowledged: {{ $ack->acknowledged_at->format('M d, Y h:i A') }}</p>
                                        <p class="text-xs text-gray-500">{{ $ack->acknowledged_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <p>No acknowledgment history found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>