{{-- resources/views/livewire/memos/show-memo.blade.php --}}
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header with Memo Number and Actions -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800">
                <div class="flex justify-between items-start flex-wrap gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($memo->priority == 'low') bg-green-100 text-green-700
                                @elseif($memo->priority == 'medium') bg-yellow-100 text-yellow-700
                                @elseif($memo->priority == 'high') bg-orange-100 text-orange-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($memo->priority) }} Priority
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($memo->status == 'draft') bg-gray-100 text-gray-700
                                @elseif($memo->status == 'pending_approval') bg-yellow-100 text-yellow-700
                                @elseif($memo->status == 'rejected') bg-red-100 text-red-700
                                @else bg-green-100 text-green-700 @endif">
                                {{ str_replace('_', ' ', ucfirst($memo->status)) }}
                            </span>
                            @if($memo->require_acknowledgment)
                                <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700">
                                    Requires Acknowledgment
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $memo->title }}</h1>
                        <p class="text-blue-100 text-sm mt-1">Memo #: {{ $memo->memo_number }}</p>
                    </div>
                    <div class="flex gap-2">
                        {{-- Download Button --}}
                        <button wire:click="downloadMemo"
                                wire:loading.attr="disabled"
                                class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download
                        </button>

                        @if(auth()->id() === $memo->created_by || auth()->user()->isAdmin())
                            <a href="{{ route('memos.edit', $memo) }}"
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                Edit Memo
                            </a>
                        @endif
                        <a href="{{ route('memos.index') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition">
                            Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <!-- Flash Messages -->
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

                <!-- Loading Indicator for Export -->
                <div wire:loading wire:target="exportAuditTrailPDF, exportAuditTrailExcel, downloadMemo" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50 flex items-center justify-center">
                    <div class="bg-white rounded-lg p-6 flex items-center gap-3">
                        <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-700">Processing...</span>
                    </div>
                </div>

                <!-- Memo Meta Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Created By</p>
                        <p class="text-sm font-medium">{{ $memo->creator->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-400">{{ $memo->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Effective Date</p>
                        <p class="text-sm font-medium">{{ $memo->effective_date ? \Carbon\Carbon::parse($memo->effective_date)->format('F d, Y') : 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Published Date</p>
                        <p class="text-sm font-medium">{{ $memo->published_at ? \Carbon\Carbon::parse($memo->published_at)->format('F d, Y h:i A') : 'Not published' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Department</p>
                        <p class="text-sm font-medium">{{ $memo->department->name ?? 'All Departments' }}</p>
                    </div>
                </div>

                <!-- Audience Information Section -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-6 border border-blue-200">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">Target Audience</h4>
                            <div class="mt-1">
                                @if($memo->audience_type === 'all')
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">All Staff</span>
                                        <span class="text-sm text-gray-600">This memo is visible to all staff members</span>
                                    </div>
                                @elseif($memo->audience_type === 'departments')
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Specific Departments</span>
                                            <span class="text-sm text-gray-600">Visible to:</span>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @php
                                                $deptIds = collect($memo->audience_ids)->where('type', 'department')->pluck('id')->toArray();
                                                $departments = App\Models\Department::whereIn('id', $deptIds)->get();
                                            @endphp
                                            @foreach($departments as $dept)
                                                <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">
                                                    {{ $dept->icon ?? '🏢' }} {{ $dept->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($memo->audience_type === 'specific_users')
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">Specific Users</span>
                                            <span class="text-sm text-gray-600">{{ count($memo->audience_ids) }} recipient(s)</span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            This memo is only visible to selected staff members
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Status Section -->
                <div class="bg-gradient-to-r from-gray-50 to-white rounded-lg p-4 mb-6 border">
                    <h3 class="text-md font-semibold text-gray-800 mb-3">Your Status</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full {{ $hasRead ? 'bg-green-500' : 'bg-red-500' }}"></div>
                            <div>
                                <span class="text-sm font-medium">Read Status:</span>
                                @if($hasRead)
                                    <span class="text-green-600 text-sm ml-1">Read on {{ \Carbon\Carbon::parse($readAt)->format('F d, Y h:i A') }}</span>
                                @else
                                    <span class="text-red-600 text-sm ml-1">Not Read Yet</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full {{ $hasAcknowledged ? 'bg-green-500' : 'bg-red-500' }}"></div>
                            <div>
                                <span class="text-sm font-medium">Acknowledgment:</span>
                                @if($hasAcknowledged)
                                    <span class="text-green-600 text-sm ml-1">Acknowledged on {{ \Carbon\Carbon::parse($acknowledgedAt)->format('F d, Y h:i A') }}</span>
                                @else
                                    <span class="text-red-600 text-sm ml-1">Not Acknowledged</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Memo Content -->
                <div class="mb-8">
                    <div class="border-b border-gray-200 pb-2 mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Memo Content
                        </h2>
                    </div>
                    <div class="bg-white border rounded-lg p-6 prose max-w-none">
                        {!! nl2br(e($memo->content)) !!}
                    </div>
                </div>

                <!-- Attachments Section -->
                @if($memo->attachments && count($memo->attachments) > 0)
                <div class="mb-8">
                    <div class="border-b border-gray-200 pb-2 mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            Attachments
                        </h2>
                    </div>
                    <div class="space-y-2">
                        @foreach($memo->attachments as $attachment)
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center space-x-3">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium">{{ $attachment['original_name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ round($attachment['size'] / 1024, 2) }} KB</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $attachment['path']) }}"
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Acknowledgment Button -->
                @if($memo->require_acknowledgment && !$hasAcknowledged && $memo->status === 'published')
                    <div class="border-t pt-6 mb-6">
                        <button wire:click="acknowledge"
                                wire:confirm="By acknowledging, you confirm that you have read and understood this memo. This will be recorded as your digital signature."
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            I Acknowledge & Digitally Sign
                        </button>
                    </div>
                @endif

                <!-- Audit Trail Section for Admin/HOD -->
                @if((auth()->user()->isAdmin() || auth()->user()->isHOD()) && $memo->status === 'published')
                    <div class="border-t pt-4">
                        <button wire:click="toggleAuditTrail"
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            {{ $showAuditTrail ? 'Hide' : 'Show' }} Staff Audit Trail
                        </button>
                        
                        <!-- Export Buttons - Always visible when audit trail is shown -->
                        @if($showAuditTrail)
                            <div class="flex gap-2 mt-3">
                                <button wire:click="exportAuditTrailPDF"
                                        wire:loading.attr="disabled"
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Export PDF
                                </button>
                                <button wire:click="exportAuditTrailExcel"
                                        wire:loading.attr="disabled"
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Excel
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Audit Trail Table (conditionally shown) -->
                    @if($showAuditTrail)
                        <div class="mt-4 border rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b">
                                <h3 class="text-md font-semibold text-gray-800">Staff Audit Trail</h3>
                                <p class="text-xs text-gray-500">Digital sign-off records for this memo</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff Name</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Read Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Read At</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acknowledged</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acknowledged At</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($memo->getAllUsersAuditTrail() as $record)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $record['user']->name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ $record['user']->department->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $record['read_at'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                        {{ $record['read_at'] ? 'Read' : 'Not Read' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ $record['read_at'] ? \Carbon\Carbon::parse($record['read_at'])->format('M d, Y h:i A') : '-' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $record['acknowledged_at'] ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                        {{ $record['acknowledged_at'] ? 'Acknowledged' : 'Pending' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('M d, Y h:i A') : '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                                    No staff have accessed this memo yet
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 border-t text-xs text-gray-500">
                                <div class="flex flex-wrap justify-between gap-2">
                                    <span>Total Staff: <strong>{{ count($memo->getAllUsersAuditTrail()) }}</strong></span>
                                    <span>Read: <strong class="text-green-600">{{ $memo->readBy()->count() }}</strong></span>
                                    <span>Acknowledged: <strong class="text-blue-600">{{ $memo->acknowledgments()->count() }}</strong></span>
                                    <span>Completion Rate: <strong class="text-purple-600">{{ $memo->acknowledgment_percentage }}%</strong></span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>