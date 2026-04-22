{{-- resources/views/livewire/hod/department-staff.blade.php --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Department Staff</h1>
            <p class="text-gray-600 mt-1">{{ $departmentName }} - Staff Management</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-blue-500">
                <div class="text-2xl mb-1">👥</div>
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_staff'] ?? 0 }}</div>
                <div class="text-xs text-gray-500">Total Staff</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-green-500">
                <div class="text-2xl mb-1">📄</div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['total_memos'] ?? 0 }}</div>
                <div class="text-xs text-gray-500">Department Memos</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-yellow-500">
                <div class="text-2xl mb-1">⏳</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['unacknowledged'] ?? 0 }}</div>
                <div class="text-xs text-gray-500">Unacknowledged</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-red-500">
                <div class="text-2xl mb-1">✏️</div>
                <div class="text-2xl font-bold text-red-600">{{ $stats['pending_memos'] ?? 0 }}</div>
                <div class="text-xs text-gray-500">Pending Memos</div>
            </div>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Search staff by name, email or staff number..." 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
        </div>

        <!-- Staff Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acknowledgment Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unread Memos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($staff as $member)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="{{ $member->profile_photo_url }}" class="h-8 w-8 rounded-full object-cover">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $member->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $member->staff_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $member->position ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($member->is_active) bg-green-100 text-green-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ $member->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 rounded-full h-2" style="width: {{ $member->acknowledgment_rate }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600">{{ $member->acknowledgment_rate }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($member->unacknowledged_memos > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $member->unacknowledged_memos }} pending
                                        </span>
                                    @else
                                        <span class="text-xs text-green-600">✓ All acknowledged</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('hod.staff.show', $member) }}" 
                                       class="text-blue-600 hover:text-blue-800">View Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No staff members found in your department
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Links -->
            @if($staff->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $staff->links() }}
                </div>
            @endif
        </div>
    </div>
</div>