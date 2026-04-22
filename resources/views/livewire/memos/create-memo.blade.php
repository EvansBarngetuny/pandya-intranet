{{-- resources/views/livewire/memos/create-memo.blade.php --}}
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800">
                <h1 class="text-2xl font-bold text-white">Create New Memo</h1>
                <p class="text-blue-100 text-sm mt-1">Fill in the details below to create a new memo</p>
            </div>

            <div class="p-6">
                @if (session()->has('message'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="publish" class="space-y-6">
                    <!-- Memo Number Display -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Memo Number</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $memo_number }}</p>
                            </div>
                            <button type="button" wire:click="generateMemoNumber"
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                Regenerate
                            </button>
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Memo Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="title"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Enter memo title">
                        @error('title') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Priority and Department -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                            <select wire:model="priority" class="w-full rounded-lg border-gray-300">
                                <option value="low">Low Priority</option>
                                <option value="medium">Medium Priority</option>
                                <option value="high">High Priority</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            @error('priority') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department (Optional)</label>
                            <select wire:model="department_id" class="w-full rounded-lg border-gray-300">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Effective Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="effective_date" class="w-full rounded-lg border-gray-300">
                            @error('effective_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input type="date" wire:model="expiry_date" class="w-full rounded-lg border-gray-300">
                            @error('expiry_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Memo Content <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="content" rows="8"
                                  class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Write your memo content here..."></textarea>
                        @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Recipients -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recipients</h3>

                        <div class="space-y-4">
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" value="all" wire:model="recipient_type" class="form-radio">
                                    <span class="ml-2">All Staff</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" value="departments" wire:model="recipient_type" class="form-radio">
                                    <span class="ml-2">Specific Departments</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" value="specific_users" wire:model="recipient_type" class="form-radio">
                                    <span class="ml-2">Specific Users</span>
                                </label>
                            </div>

                            @if($recipient_type === 'departments')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Departments</label>
                                    <div class="grid grid-cols-2 gap-3 max-h-48 overflow-y-auto p-2 border rounded">
                                        @foreach($departments as $dept)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" value="{{ $dept->id }}"
                                                       wire:model="selected_departments" class="form-checkbox">
                                                <span class="ml-2 text-sm">{{ $dept->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('selected_departments')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if($recipient_type === 'specific_users')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Users</label>
                                    <div class="grid grid-cols-2 gap-3 max-h-48 overflow-y-auto p-2 border rounded">
                                        @foreach($users as $user)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" value="{{ $user->id }}"
                                                       wire:model="selected_users" class="form-checkbox">
                                                <span class="ml-2 text-sm">
                                                    {{ $user->name }}
                                                    <span class="text-gray-500 text-xs">
                                                        ({{ $user->department->name ?? 'No Dept' }})
                                                    </span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('selected_users')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Attachments</h3>

                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload files</span>
                                        <input type="file" wire:model="attachments" multiple class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PDF, DOC, DOCX, JPG, PNG up to 10MB
                                </p>
                            </div>
                        </div>

                        @if(count($attachments) > 0)
                            <div class="mt-4 space-y-2">
                                @foreach($attachments as $index => $attachment)
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                        <div class="flex items-center space-x-2">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="text-sm">{{ $attachment->getClientOriginalName() }}</span>
                                        </div>
                                        <button type="button" wire:click="removeAttachment({{ $index }})"
                                                class="text-red-600 hover:text-red-800">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Options -->
                    <div class="border-t pt-6">
                        <label class="inline-flex items-center">
                            <input type="checkbox" wire:model="require_acknowledgment" class="form-checkbox">
                            <span class="ml-2 text-sm text-gray-700">Require acknowledgment of receipt</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t">
                        <a href="{{ route('memos.index') }}"
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                            Cancel
                        </a>
                        <button type="button" wire:click="saveAsDraft"
                                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                            Save as Draft
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Publish Memo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
