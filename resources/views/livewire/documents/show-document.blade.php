{{-- resources/views/livewire/documents/show-document.blade.php --}}
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800">
                <div class="flex justify-between items-start flex-wrap gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                {{ strtoupper(str_replace('_', ' ', $document->category)) }}
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                                v{{ $document->version }}
                            </span>
                            @if($document->require_acknowledgment)
                                <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700">
                                    Requires Acknowledgment
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $document->title }}</h1>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="download"
                                class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download
                        </button>
                        <a href="{{ route('documents.index') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition">
                            Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <!-- User Status Section -->
                <div class="bg-gradient-to-r from-gray-50 to-white rounded-lg p-4 mb-6 border">
                    <h3 class="text-md font-semibold text-gray-800 mb-3">Your Status</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full {{ $hasViewed ? 'bg-green-500' : 'bg-red-500' }}"></div>
                            <div>
                                <span class="text-sm font-medium">Viewed:</span>
                                @if($hasViewed)
                                    <span class="text-green-600 text-sm ml-1">{{ \Carbon\Carbon::parse($viewedAt)->format('M d, Y h:i A') }}</span>
                                @else
                                    <span class="text-red-600 text-sm ml-1">Not Viewed</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full {{ $hasAcknowledged ? 'bg-green-500' : ($document->require_acknowledgment ? 'bg-red-500' : 'bg-gray-400') }}"></div>
                            <div>
                                <span class="text-sm font-medium">Acknowledged:</span>
                                @if($hasAcknowledged)
                                    <span class="text-green-600 text-sm ml-1">{{ \Carbon\Carbon::parse($acknowledgedAt)->format('M d, Y h:i A') }}</span>
                                @elseif($document->require_acknowledgment)
                                    <span class="text-red-600 text-sm ml-1">Not Acknowledged</span>
                                @else
                                    <span class="text-gray-500 text-sm ml-1">Not Required</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <div>
                                <span class="text-sm font-medium">Downloads:</span>
                                <span class="text-blue-600 text-sm ml-1">{{ $document->download_count }} times</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-xs text-gray-500">File Name</p>
                        <p class="text-sm font-medium">{{ $document->file_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">File Size</p>
                        <p class="text-sm font-medium">{{ $document->file_size }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Uploaded By</p>
                        <p class="text-sm font-medium">{{ $document->uploader->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Uploaded Date</p>
                        <p class="text-sm font-medium">{{ $document->created_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Version</p>
                        <p class="text-sm font-medium">{{ $document->version }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Effective Date</p>
                        <p class="text-sm font-medium">{{ $document->effective_date ? \Carbon\Carbon::parse($document->effective_date)->format('F d, Y') : 'N/A' }}</p>
                    </div>
                </div>

                <!-- Description -->
                @if($document->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                        <p class="text-gray-600">{{ $document->description }}</p>
                    </div>
                @endif

                <!-- Acknowledgment Button -->
                @if($document->require_acknowledgment && !$hasAcknowledged)
                    <div class="border-t pt-6 mb-6">
                        <button wire:click="acknowledge"
                                wire:confirm="By acknowledging, you confirm that you have read and understood this document."
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                            ✅ I Acknowledge This Document
                        </button>
                    </div>
                @endif

                <!-- Audit Trail for Admin/HOD -->
                <!-- Audit Trail for Admin/HOD -->
@if(auth()->user()->isAdmin() || auth()->user()->isHOD())
    <div class="border-t pt-4">
        <button wire:click="toggleAuditTrail"
                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            {{ $showAuditTrail ? 'Hide' : 'Show' }} Staff Audit Trail
        </button>

        @if($showAuditTrail)
            <div class="mt-4 border rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b">
                    <h3 class="text-md font-semibold text-gray-800">Staff Audit Trail</h3>
                    <p class="text-xs text-gray-500">
                        @if(auth()->user()->isAdmin())
                            All staff members
                        @else
                            Your department staff only
                        @endif
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Staff Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Department</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Viewed</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Viewed At</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Acknowledged</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Acknowledged At</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Downloaded</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($auditTrail as $record)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">{{ $record['user']->name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $record['user']->department->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $record['viewed_at'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $record['viewed_at'] ? 'Viewed' : 'Not Viewed' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $record['viewed_at'] ? \Carbon\Carbon::parse($record['viewed_at'])->format('M d, Y h:i A') : '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $record['acknowledged_at'] ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $record['acknowledged_at'] ? 'Acknowledged' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('M d, Y h:i A') : '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($record['downloaded'])
                                            <span class="text-green-600">✓ Yes</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        @if(auth()->user()->isAdmin())
                                            No records found
                                        @else
                                            No staff records found for your department
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-4 py-3 border-t text-xs text-gray-500">
                    <div class="flex justify-between">
                        <span>Total Staff: {{ count($auditTrail) }}</span>
                        <span>Viewed: {{ collect($auditTrail)->where('viewed_at', '!=', null)->count() }}</span>
                        <span>Acknowledged: {{ collect($auditTrail)->where('acknowledged_at', '!=', null)->count() }}</span>
                        <span>Downloads: {{ $document->download_count }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
            </div>
        </div>
    </div>
</div>
