{{-- resources/views/livewire/admin/staff-index.blade.php --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Staff Management</h1>
                <p class="text-gray-600 mt-1">Manage hospital staff, roles, and permissions</p>
            </div>
            <a href="{{ route('admin.staff.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Staff
            </a>
        </div>

        <!-- Flash Messages -->
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl mb-1">👥</div>
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                <div class="text-xs text-gray-500">Total Staff</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl mb-1">✅</div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</div>
                <div class="text-xs text-gray-500">Active</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl mb-1">👑</div>
                <div class="text-2xl font-bold text-purple-600">{{ $stats['admin'] }}</div>
                <div class="text-xs text-gray-500">Admins</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl mb-1">👔</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['hod'] }}</div>
                <div class="text-xs text-gray-500">HODs</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl mb-1">👤</div>
                <div class="text-2xl font-bold text-gray-600">{{ $stats['staff'] }}</div>
                <div class="text-xs text-gray-500">Staff</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Search by name, email, staff #..." 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <select wire:model.live="department_filter" class="w-full rounded-lg border-gray-300">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select wire:model.live="role_filter" class="w-full rounded-lg border-gray-300">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="hod">Head of Department</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div>
                    <select wire:model.live="status_filter" class="w-full rounded-lg border-gray-300">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Staff Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($staff as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="{{ $user->profile_photo_url }}" class="h-8 w-8 rounded-full object-cover">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->staff_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->department->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($user->role == 'admin') bg-purple-100 text-purple-700
                                        @elseif($user->role == 'hod') bg-yellow-100 text-yellow-700
                                        @else bg-blue-100 text-blue-700 @endif">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($user->is_active) bg-green-100 text-green-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.staff.edit', $user) }}" 
                                           class="text-blue-600 hover:text-blue-800">Edit</a>
                                        <button wire:click="toggleUserStatus({{ $user->id }})"
                                                class="text-yellow-600 hover:text-yellow-800">
                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                        @if(auth()->id() !== $user->id)
                                            <button wire:click="deleteUser({{ $user->id }})"
                                                    wire:confirm="Are you sure you want to delete this user?"
                                                    class="text-red-600 hover:text-red-800">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No staff members found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t">
                {{ $staff->links() }}
            </div>
        </div>
    </div>
</div>