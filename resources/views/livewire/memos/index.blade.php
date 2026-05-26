{{-- resources/views/livewire/memos/index.blade.php --}}
<div class="p-4">
    <!-- Header -->
   <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold">Memos</h2>
    @if(auth()->user()->isHOD() || auth()->user()->isAdmin())
        <a href="{{ route('memos.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            + Create Memo
        </a>
    @endif
</div>

    <!-- Flash message -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-3">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-wrap gap-2 mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search memos..."
               class="border border-gray-300 p-2 rounded-lg w-full md:w-1/3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">

        <select wire:model.live="priority" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Priorities</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
        </select>

        <select wire:model.live="status" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="pending_approval">Pending Approval</option>
            <option value="published">Published</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <!-- Loading indicator -->
    <div wire:loading class="text-center py-4">
        <div class="inline-flex items-center gap-2 text-gray-500">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading...
        </div>
    </div>

    <!-- Memo list - Make entire card clickable -->
    <div class="space-y-3" wire:loading.remove>
        @forelse($memos as $memo)
            <div class="border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-all hover:border-blue-400"
                 @click="window.location='{{ route('memos.show', $memo) }}'" style="cursor: pointer;">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <h3 class="font-bold text-lg hover:text-blue-600 transition">
                                {{ $memo->title }}
                            </h3>
                            @if($memo->priority)
                                <span class="text-xs px-2 py-1 rounded-full
                                    @if($memo->priority == 'low') bg-green-100 text-green-700
                                    @elseif($memo->priority == 'medium') bg-yellow-100 text-yellow-700
                                    @elseif($memo->priority == 'high') bg-orange-100 text-orange-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($memo->priority) }}
                                </span>
                            @endif
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($memo->status == 'draft') bg-gray-100 text-gray-700
                                @elseif($memo->status == 'pending_approval') bg-yellow-100 text-yellow-700
                                @elseif($memo->status == 'rejected') bg-red-100 text-red-700
                                @else bg-green-100 text-green-700 @endif">
                                {{ str_replace('_', ' ', ucfirst($memo->status)) }}
                            </span>

                            @if($memo->status === 'published' && !$memo->isReadBy(auth()->user()))
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                                    New
                                </span>
                            @endif

                            @if($memo->require_acknowledgment && $memo->status === 'published' && !$memo->acknowledgedBy(auth()->user()))
                                <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700">
                                    Awaiting Acknowledgment
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 mb-1">
                            Memo #: {{ $memo->memo_number }}
                        </p>

                        <p class="text-sm text-gray-600 line-clamp-2">
                            {{ Str::limit($memo->content, 100) }}
                        </p>

                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                            <span>Created: {{ $memo->created_at->format('M d, Y') }}</span>
                            @if($memo->published_at)
                                <span>Published: {{ $memo->published_at->format('M d, Y') }}</span>
                            @endif
                            <span>By: {{ $memo->creator->name ?? 'Unknown' }}</span>
                        </div>
                    </div>


                    <div class="flex gap-2 ml-4" onclick="event.stopPropagation()">
                        <!-- Draft actions -->
                        @if($memo->status === 'draft' && (auth()->id() === $memo->created_by || auth()->user()->isHOD() || auth()->user()->isAdmin()))
                            <a href="{{ route('memos.edit', $memo) }}"
                               class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-3 py-1 rounded transition">
                                Edit
                            </a>
                            <button wire:click="submitForApproval({{ $memo->id }})"
                                    wire:confirm="Submit this memo for approval?"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded transition">
                                Submit for Approval
                            </button>
                        @endif

                        <!-- Pending Approval actions (Admin only) -->
                        @if($memo->status === 'pending_approval' && auth()->user()->isAdmin())
                            <button wire:click="approveMemo({{ $memo->id }})"
                                    wire:confirm="Approve and publish this memo?"
                                    class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded transition">
                                Approve & Publish
                            </button>
                            <button wire:click="rejectMemo({{ $memo->id }})"
                                    wire:confirm="Reject this memo?"
                                    class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded transition">
                                Reject
                            </button>
                        @endif

                        <!-- Published memo actions -->
                        @if($memo->status === 'published')
                            @if(!$memo->isReadBy(auth()->user()))
                                <button wire:click="markAsRead({{ $memo->id }})"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded transition">
                                    Mark as Read
                                </button>
                            @endif

                            @if($memo->isReadBy(auth()->user()))
                                <span class="text-green-600 text-sm flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Read
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <svg class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p>No memos found</p>
                <p class="text-sm mt-1">Try adjusting your search or create a new memo</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($memos->hasPages())
        <div class="mt-6">
            {{ $memos->links() }}
        </div>
    @endif
</div>
