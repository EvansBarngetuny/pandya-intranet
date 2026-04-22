{{-- resources/views/livewire/news/show-news.blade.php --}}
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header with back button -->
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b">
                <div class="flex justify-between items-center">
                    <a href="{{ route('news.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to News
                    </a>
                    
                    @if(auth()->user()->isAdmin())
                        <div class="flex gap-2">
                            <button class="text-red-600 hover:text-red-800">Delete</button>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Featured Image -->
            @if($news->featured_image)
                <img src="{{ asset('storage/' . $news->featured_image) }}" 
                     alt="{{ $news->title }}"
                     class="w-full h-64 md:h-96 object-cover">
            @endif
            
            <!-- Content -->
            <div class="p-6 md:p-8">
                <!-- Meta Info -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="text-2xl">
                        @switch($news->category)
                            @case('announcement') 📢 @break
                            @case('achievement') 🏆 @break
                            @case('facility') 🏥 @break
                            @default 📰
                        @endswitch
                    </span>
                    <span class="text-sm px-2 py-1 rounded-full 
                        @if($news->category === 'announcement') bg-blue-100 text-blue-700
                        @elseif($news->category === 'achievement') bg-yellow-100 text-yellow-700
                        @elseif($news->category === 'facility') bg-green-100 text-green-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ ucfirst($news->category) }}
                    </span>
                    @if($news->is_pinned)
                        <span class="text-sm bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full flex items-center gap-1">
                            📌 Pinned
                        </span>
                    @endif
                </div>
                
                <!-- Title -->
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $news->title }}</h1>
                
                <!-- Author & Date -->
                <div class="flex items-center gap-4 text-sm text-gray-500 mb-6 pb-6 border-b">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-600 font-bold">
                                {{ substr($news->author->name, 0, 1) }}
                            </span>
                        </div>
                        <span>{{ $news->author->name }}</span>
                    </div>
                    <span>•</span>
                    <span>{{ $news->published_at->format('F d, Y') }}</span>
                    <span>•</span>
                    <span>{{ $news->published_at->format('h:i A') }}</span>
                </div>
                
                <!-- Summary -->
                @if($news->summary)
                    <div class="bg-gray-50 border-l-4 border-blue-500 p-4 mb-6 italic text-gray-700">
                        {{ $news->summary }}
                    </div>
                @endif
                
                <!-- Content -->
                <div class="prose max-w-none">
                    {!! nl2br(e($news->content)) !!}
                </div>
                
                <!-- Tags -->
                @if($news->tags && count($news->tags) > 0)
                    <div class="mt-8 pt-6 border-t">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($news->tags as $tag)
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>