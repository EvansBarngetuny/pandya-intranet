{{-- resources/views/livewire/admin/departments.blade.php --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Departments</h1>
                <p class="text-gray-600 mt-1">Manage hospital departments and HOD assignments</p>
            </div>
            <button wire:click="$set('showCreateForm', true)" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                + Add Department
            </button>
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

        <!-- Departments Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($departments as $dept)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="text-4xl">{{ $dept->icon }}</div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">{{ $dept->name }}</h3>
                                    <p class="text-xs text-gray-500">Code: {{ $dept->code }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="editDepartment({{ $dept->id }})" 
                                        class="text-blue-600 hover:text-blue-800">Edit</button>
                                <button wire:click="deleteDepartment({{ $dept->id }})"
                                        wire:confirm="Are you sure? This will remove the department."
                                        class="text-red-600 hover:text-red-800">Delete</button>
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Head of Department:</span>
                                <span class="font-medium">{{ $dept->hod_name ?? 'Not assigned' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Staff Count:</span>
                                <span class="font-medium">{{ $dept->users_count }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Create Department Modal -->
        @if($showCreateForm)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                    <h2 class="text-xl font-bold mb-4">Add New Department</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Department Name *</label>
                            <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">Department Code *</label>
                            <input type="text" wire:model="code" placeholder="e.g., HR, IT, FIN" class="w-full rounded-lg border-gray-300">
                            @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">Head of Department</label>
                            <input type="text" wire:model="hod_name" class="w-full rounded-lg border-gray-300">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">Icon (Emoji)</label>
                            <input type="text" wire:model="icon" maxlength="2" class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-6">
                        <button wire:click="$set('showCreateForm', false)" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg">Cancel</button>
                        <button wire:click="createDepartment" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg">Create</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Department Modal -->
        @if($showEditForm && $editingDepartment)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                    <h2 class="text-xl font-bold mb-4">Edit Department</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Department Name *</label>
                            <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">Department Code *</label>
                            <input type="text" wire:model="code" class="w-full rounded-lg border-gray-300">
                            @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">Head of Department</label>
                            <input type="text" wire:model="hod_name" class="w-full rounded-lg border-gray-300">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">Icon (Emoji)</label>
                            <input type="text" wire:model="icon" maxlength="2" class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-6">
                        <button wire:click="$set('showEditForm', false)" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg">Cancel</button>
                        <button wire:click="updateDepartment" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>