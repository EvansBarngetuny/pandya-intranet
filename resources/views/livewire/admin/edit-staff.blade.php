{{-- resources/views/livewire/admin/edit-staff.blade.php --}}
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-orange-500">
                <h1 class="text-2xl font-bold text-white">Edit Staff Member</h1>
                <p class="text-yellow-100 text-sm mt-1">Update staff information and permissions</p>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="update" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                <input type="email" wire:model="email" class="w-full rounded-lg border-gray-300">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Staff Number</label>
                                <input type="text" wire:model="staff_number" class="w-full rounded-lg border-gray-300 bg-gray-100" readonly>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" wire:model="phone" class="w-full rounded-lg border-gray-300">
                            </div>
                        </div>
                    </div>

                    <!-- Role & Department -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Role & Department</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                <select wire:model="role" class="w-full rounded-lg border-gray-300">
                                    <option value="staff">Staff Member</option>
                                    <option value="hod">Head of Department (HOD)</option>
                                    <option value="admin">Administrator</option>
                                </select>
                                @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <select wire:model="department_id" class="w-full rounded-lg border-gray-300">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Position/Title</label>
                                <input type="text" wire:model="position" class="w-full rounded-lg border-gray-300">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hire Date</label>
                                <input type="date" wire:model="hire_date" class="w-full rounded-lg border-gray-300">
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password (Optional)</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input type="password" wire:model="password" class="w-full rounded-lg border-gray-300">
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input type="password" wire:model="password_confirmation" class="w-full rounded-lg border-gray-300">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Leave blank to keep current password</p>
                    </div>

                    <!-- Profile Photo -->
                    <div class="border-b pb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                        <div class="flex items-center gap-4">
                            @if($profile_photo)
                                <img src="{{ $profile_photo->temporaryUrl() }}" class="h-16 w-16 rounded-full object-cover">
                            @elseif($existing_photo)
                                <img src="{{ asset('storage/' . $existing_photo) }}" class="h-16 w-16 rounded-full object-cover">
                            @else
                                <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-2xl">👤</span>
                                </div>
                            @endif
                            <input type="file" wire:model="profile_photo" accept="image/*">
                        </div>
                        @error('profile_photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" wire:model="is_active" class="form-checkbox text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Active (user can log in)</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.staff.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Update Staff
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>