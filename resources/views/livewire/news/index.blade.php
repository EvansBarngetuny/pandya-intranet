{{-- resources/views/livewire/news/index.blade.php --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">News & Announcements</h1>
                <p class="text-gray-600 mt-1">Stay updated with hospital announcements and achievements</p>
            </div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('news.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Post News
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
                           placeholder="Search news..." 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <select wire:model.live="category" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <option value="announcement">📢 Announcement</option>
                        <option value="achievement">🏆 Achievement</option>
                        <option value="facility">🏥 Facility Update</option>
                        <option value="general">📰 General</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- News Grid -->
        <div class="space-y-6">
            @forelse($news as $item)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    @if($item->featured_image)
                        <img src="{{ asset('storage/' . $item->featured_image) }}" 
                             alt="{{ $item->title }}"
                             class="w-full h-48 object-cover">
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-2xl">
                                        @switch($item->category)
                                            @case('announcement') 📢 @break
                                            @case('achievement') 🏆 @break
                                            @case('facility') 🏥 @break
                                            @default 📰
                                        @endswitch
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        @if($item->category === 'announcement') bg-blue-100 text-blue-700
                                        @elseif($item->category === 'achievement') bg-yellow-100 text-yellow-700
                                        @elseif($item->category === 'facility') bg-green-100 text-green-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst($item->category) }}
                                    </span>
                                    @if($item->is_pinned)
                                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full flex items-center gap-1">
                                            📌 Pinned
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500">
                                        {{ $item->published_at->diffForHumans() }}
                                    </span>
                                </div>
                                
                                <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $item->title }}</h2>
                                <p class="text-gray-600 mb-4">{{ $item->summary ?: Str::limit($item->content, 150) }}</p>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-sm text-gray-500">
                                        <span>By {{ $item->author->name }}</span>
                                        <span>•</span>
                                        <span>{{ $item->published_at->format('M d, Y') }}</span>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <a href="{{ route('news.show', $item) }}" 
                                           class="text-blue-600 hover:text-blue-800 font-medium">
                                            Read more →
                                        </a>
                                        
                                        @if(auth()->user()->isAdmin())
                                            <button wire:click="togglePin({{ $item->id }})" 
                                                    class="text-yellow-600 hover:text-yellow-800">
                                                {{ $item->is_pinned ? 'Unpin' : 'Pin' }}
                                            </button>
                                            <button wire:click="deleteNews({{ $item->id }})"
                                                    wire:confirm="Are you sure you want to delete this news?"
                                                    class="text-red-600 hover:text-red-800">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-6xl mb-4">📰</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No News Found</h3>
                    <p class="text-gray-500">Check back later for updates and announcements</p>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('news.create') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                            + Post the first news
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($news->hasPages())
            <div class="mt-6">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</div>