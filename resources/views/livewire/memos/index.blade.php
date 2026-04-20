<div class="p-4">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Memos</h2>

        <button
            wire:click="$set('showCreateForm', true)"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
        >
            + Create Memo
        </button>
    </div>

    {{-- Flash message --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-3">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filters --}}
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
            <option value="published">Published</option>
        </select>
    </div>

    {{-- Loading indicator --}}
    <div wire:loading class="text-center py-4">
        <div class="inline-flex items-center gap-2 text-gray-500">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading...
        </div>
    </div>

    {{-- Memo list --}}
    <div class="space-y-3" wire:loading.remove>
        @forelse($memos as $memo)
            <div class="border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="font-bold text-lg">{{ $memo->title }}</h3>
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
                                @else bg-green-100 text-green-700 @endif">
                                {{ ucfirst($memo->status) }}
                            </span>
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

                    <div class="flex gap-2 ml-4">
                        @if($memo->status === 'draft')
                            <button wire:click="publishMemo({{ $memo->id }})"
                                    wire:confirm="Are you sure you want to publish this memo?"
                                    class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded transition">
                                Publish
                            </button>
                        @endif

                        @if($memo->status === 'published' && !$memo->readBy->contains(auth()->id()))
                            <button wire:click="markAsRead({{ $memo->id }})"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded transition">
                                Mark as Read
                            </button>
                        @endif

                        @if($memo->readBy->contains(auth()->id()))
                            <span class="text-green-600 text-sm flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Read
                            </span>
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

    {{-- Pagination --}}
    @if($memos->hasPages())
        <div class="mt-6">
            {{ $memos->links() }}
        </div>
    @endif

    {{-- CREATE MODAL --}}
    @if($showCreateForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Create New Memo</h2>
                    <button wire:click="$set('showCreateForm', false)" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Memo Number *</label>
                        <input wire:model="memo_number" placeholder="e.g., MEMO-2024-001"
                               class="border border-gray-300 p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('memo_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Title *</label>
                        <input wire:model="title" placeholder="Memo title"
                               class="border border-gray-300 p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Content *</label>
                        <textarea wire:model="content" placeholder="Memo content" rows="5"
                                  class="border border-gray-300 p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Priority *</label>
                            <select wire:model="priority" class="border border-gray-300 p-2 w-full rounded-lg">
                                <option value="">Select Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            @error('priority') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Department</label>
                            <select wire:model="department_id" class="border border-gray-300 p-2 w-full rounded-lg">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Effective Date *</label>
                            <input type="date" wire:model="effective_date"
                                   class="border border-gray-300 p-2 w-full rounded-lg">
                            @error('effective_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Expiry Date</label>
                            <input type="date" wire:model="expiry_date"
                                   class="border border-gray-300 p-2 w-full rounded-lg">
                            @error('expiry_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button wire:click="$set('showCreateForm', false)"
                            class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg transition">
                        Cancel
                    </button>
                    <button wire:click="createMemo"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Create Memo
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
