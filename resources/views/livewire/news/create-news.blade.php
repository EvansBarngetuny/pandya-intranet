{{-- resources/views/livewire/news/create-news.blade.php --}}
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-teal-600">
                <h1 class="text-2xl font-bold text-white">Post News & Announcement</h1>
                <p class="text-green-100 text-sm mt-1">Share important updates with hospital staff</p>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form wire:submit.prevent="save" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            News Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               wire:model="title" 
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                               placeholder="Enter news title">
                        @error('title') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category and Pinned -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select wire:model="category" class="w-full rounded-lg border-gray-300 focus:ring-green-500">
                                <option value="general">📰 General News</option>
                                <option value="announcement">📢 Announcement</option>
                                <option value="achievement">🏆 Achievement</option>
                                <option value="facility">🏥 Facility Update</option>
                            </select>
                            @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="is_pinned" class="form-checkbox text-green-600">
                                <span class="ml-2 text-sm text-gray-700">Pin this news (appears at top)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image (Optional)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                @if($featured_image)
                                    <div class="mb-3">
                                        <img src="{{ $featured_image->temporaryUrl() }}" 
                                             class="mx-auto h-32 w-auto object-cover rounded">
                                    </div>
                                @endif
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500">
                                        <span>Upload an image</span>
                                        <input type="file" wire:model="featured_image" accept="image/*" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        @error('featured_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Summary -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Summary (Optional)</label>
                        <textarea wire:model="summary" rows="2" 
                                  class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                  placeholder="Brief summary of the news (will be shown in preview)"></textarea>
                        <p class="text-xs text-gray-500 mt-1">If left empty, first 150 characters of content will be used</p>
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            News Content <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="content" rows="10" 
                                  class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 font-mono"
                                  placeholder="Write your news content here..."></textarea>
                        @error('content') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tags -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   wire:model="tagInput" 
                                   wire:keydown.enter="addTag"
                                   class="flex-1 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                   placeholder="Add tags (press Enter)">
                            <button type="button" wire:click="addTag" 
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                Add
                            </button>
                        </div>
                        
                        @if(count($tags) > 0)
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach($tags as $index => $tag)
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-sm">
                                        {{ $tag }}
                                        <button type="button" wire:click="removeTag({{ $index }})" class="text-red-500 hover:text-red-700">
                                            ×
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t">
                        <a href="{{ route('news.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <span wire:loading.remove>Publish News</span>
                            <span wire:loading>Publishing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>