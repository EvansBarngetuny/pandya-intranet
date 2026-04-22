{{-- resources/views/livewire/documents/index.blade.php --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Documents & Policies</h1>
                <p class="text-gray-600 mt-1">Access hospital policies, SOPs, and forms</p>
            </div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('documents.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Upload Document
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

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Search documents..." 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <select wire:model.live="category" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <option value="sop">📋 Standard Operating Procedures</option>
                        <option value="policy">📜 Hospital Policies</option>
                        <option value="form">📝 Forms & Templates</option>
                        <option value="guideline">📖 Clinical Guidelines</option>
                        <option value="manual">📘 Staff Manuals</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Documents Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($documents as $doc)
                <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="text-4xl">
                                @switch($doc->category)
                                    @case('sop') 📋 @break
                                    @case('policy') 📜 @break
                                    @case('form') 📝 @break
                                    @case('guideline') 📖 @break
                                    @case('manual') 📘 @break
                                    @default 📄
                                @endswitch
                            </div>
                            @if(auth()->user()->isAdmin() || $doc->uploaded_by === auth()->id())
                                <button wire:click="deleteDocument({{ $doc->id }})"
                                        wire:confirm="Are you sure you want to delete this document?"
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $doc->title }}</h3>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $doc->description ?? 'No description' }}</p>
                        
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            <span>Version {{ $doc->version }}</span>
                            <span>{{ $doc->file_size }}</span>
                            <span>📥 {{ $doc->download_count }}</span>
                        </div>
                        
                        <div class="flex gap-2">
                            <button wire:click="download({{ $doc->id }})"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition text-center">
                                Download
                            </button>
                            <a href="{{ route('documents.show', $doc) }}" 
                               class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm transition text-center">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-6xl mb-4">📚</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No Documents Found</h3>
                    <p class="text-gray-500">No documents match your search criteria</p>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('documents.create') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                            + Upload the first document
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="mt-6">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>