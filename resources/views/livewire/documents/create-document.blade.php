{{-- resources/views/livewire/documents/create-document.blade.php --}}
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800">
                <h1 class="text-2xl font-bold text-white">Upload Document</h1>
                <p class="text-blue-100 text-sm mt-1">Add policies, SOPs, or forms to the document repository</p>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Document Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               wire:model="title" 
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Enter document title">
                        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="category" class="w-full rounded-lg border-gray-300">
                            <option value="sop">📋 Standard Operating Procedures (SOP)</option>
                            <option value="policy">📜 Hospital Policies</option>
                            <option value="form">📝 Forms & Templates</option>
                            <option value="guideline">📖 Clinical Guidelines</option>
                            <option value="manual">📘 Staff Manuals</option>
                        </select>
                        @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea wire:model="description" rows="3" 
                                  class="w-full rounded-lg border-gray-300"
                                  placeholder="Brief description of the document"></textarea>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            File <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                @if($file)
                                    <div class="text-green-600 mb-2">
                                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-sm">{{ $file->getClientOriginalName() }}</p>
                                        <p class="text-xs text-gray-500">{{ $this->formatBytes($file->getSize()) }}</p>
                                    </div>
                                @endif
                                <div class="flex text-sm text-gray-600">
                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>{{ $file ? 'Change file' : 'Upload a file' }}</span>
                                        <input type="file" wire:model="file" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT up to 10MB
                                </p>
                            </div>
                        </div>
                        @error('file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Version & Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                            <input type="number" wire:model="version" min="1" class="w-full rounded-lg border-gray-300">
                            @error('version') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Effective Date</label>
                            <input type="date" wire:model="effective_date" class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" wire:model="is_active" class="form-checkbox text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Active (document is visible to staff)</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('documents.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Upload Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>
@endpush