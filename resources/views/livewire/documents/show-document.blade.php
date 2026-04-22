{{-- resources/views/livewire/documents/show-document.blade.php --}}
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800">
                <div class="flex justify-between items-center">
                    <a href="{{ route('documents.index') }}" class="text-white hover:text-blue-200 flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Documents
                    </a>
                    
                    @if(auth()->user()->isAdmin() || $document->uploaded_by === auth()->id())
                        <button wire:click="$dispatch('delete-document', { id: {{ $document->id }} })"
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                            Delete Document
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="p-6 md:p-8">
                <!-- Document Icon & Title -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="text-6xl">
                        @switch($document->category)
                            @case('sop') 📋 @break
                            @case('policy') 📜 @break
                            @case('form') 📝 @break
                            @case('guideline') 📖 @break
                            @case('manual') 📘 @break
                            @default 📄
                        @endswitch
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $document->title }}</h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                {{ ucfirst(str_replace('_', ' ', $document->category)) }}
                            </span>
                            <span class="text-sm text-gray-500">Version {{ $document->version }}</span>
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
                    @if($document->effective_date)
                    <div>
                        <p class="text-xs text-gray-500">Effective Date</p>
                        <p class="text-sm font-medium">{{ \Carbon\Carbon::parse($document->effective_date)->format('F d, Y') }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-500">Downloads</p>
                        <p class="text-sm font-medium">{{ $document->download_count }} times</p>
                    </div>
                </div>
                
                <!-- Description -->
                @if($document->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                        <p class="text-gray-600">{{ $document->description }}</p>
                    </div>
                @endif
                
                <!-- Download Button -->
                <div class="border-t pt-6">
                    <button wire:click="download" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Document
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>