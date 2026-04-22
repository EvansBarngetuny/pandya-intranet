{{-- resources/views/livewire/memos/show-memo.blade.php --}}
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($memo->priority == 'low') bg-green-100 text-green-700
                                @elseif($memo->priority == 'medium') bg-yellow-100 text-yellow-700
                                @elseif($memo->priority == 'high') bg-orange-100 text-orange-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($memo->priority) }} Priority
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($memo->status == 'draft') bg-gray-100 text-gray-700
                                @else bg-green-100 text-green-700 @endif">
                                {{ ucfirst($memo->status) }}
                            </span>
                        </div>
                        <h1 class="text-2xl font-bold text-white">{{ $memo->title }}</h1>
                        <p class="text-blue-100 text-sm mt-1">Memo #: {{ $memo->memo_number }}</p>
                    </div>
                    <div class="flex gap-2">
                        @if(auth()->id() === $memo->created_by || auth()->user()->isAdmin())
                            <a href="{{ route('memos.edit', $memo) }}" 
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                Edit Memo
                            </a>
                        @endif
                        <a href="{{ route('memos.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if (session()->has('message'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Memo Meta Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Created By</p>
                            <p class="text-sm font-medium">{{ $memo->creator->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Effective Date</p>
                            <p class="text-sm font-medium">{{ $memo->effective_date?->format('F d, Y') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Published Date</p>
                            <p class="text-sm font-medium">{{ $memo->published_at?->format('F d, Y') ?? 'Not published' }}</p>
                        </div>
                        @if($memo->department)
                        <div>
                            <p class="text-xs text-gray-500">Department</p>
                            <p class="text-sm font-medium">{{ $memo->department->name }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-500">Requires Acknowledgment</p>
                            <p class="text-sm font-medium">{{ $memo->require_acknowledgment ? 'Yes' : 'No' }}</p>
                        </div>
                        @if($hasAcknowledged)
                        <div>
                            <p class="text-xs text-gray-500">Acknowledged On</p>
                            <p class="text-sm font-medium text-green-600">
                                {{ $memo->acknowledgments()->where('user_id', auth()->id())->first()?->acknowledged_at?->format('F d, Y h:i A') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Memo Content -->
                <div class="prose max-w-none mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Memo Content</h3>
                    <div class="bg-white border rounded-lg p-6">
                        {!! nl2br(e($memo->content)) !!}
                    </div>
                </div>

                <!-- Attachments -->
                @if($memo->attachments && count($memo->attachments) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Attachments</h3>
                    <div class="space-y-2">
                        @foreach($memo->attachments as $attachment)
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium">{{ $attachment['original_name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ round($attachment['size'] / 1024, 2) }} KB</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition">
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Acknowledgment Button -->
                @if($memo->require_acknowledgment && !$hasAcknowledged && $memo->status === 'published')
                    <div class="border-t pt-6">
                        <button wire:click="acknowledge"
                                wire:confirm="By acknowledging, you confirm that you have read and understood this memo."
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                            ✅ I Acknowledge That I Have Read This Memo
                        </button>
                    </div>
                @endif

                <!-- Acknowledgment List for Admin/HOD -->
                @if((auth()->user()->isAdmin() || auth()->user()->isHOD()) && $memo->require_acknowledgment)
                    <div class="border-t pt-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Acknowledgment Status</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="mb-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Overall Acknowledgment Rate</span>
                                    <span>{{ $memo->acknowledgment_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 rounded-full h-2" style="width: {{ $memo->acknowledgment_percentage }}%"></div>
                                </div>
                            </div>
                            
                            @php
                                $unacknowledged = $memo->getUnacknowledgedUsers();
                            @endphp
                            
                            @if(count($unacknowledged) > 0)
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Staff who haven't acknowledged:</p>
                                    <div class="max-h-48 overflow-y-auto space-y-1">
                                        @foreach($unacknowledged as $user)
                                            <div class="flex items-center justify-between text-sm p-2 bg-white rounded">
                                                <span>{{ $user->name }}</span>
                                                <span class="text-xs text-gray-500">{{ $user->department->name ?? 'No Dept' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p class="text-green-600 text-sm mt-3">✓ All recipients have acknowledged this memo</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>