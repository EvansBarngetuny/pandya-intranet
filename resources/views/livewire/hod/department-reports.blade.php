{{-- resources/views/livewire/hod/department-reports.blade.php --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Department Reports</h1>
            <p class="text-gray-600 mt-1">{{ $departmentName }} - Analytics & Insights</p>
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
                <div class="text-xs text-gray-500">Total Memos</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-purple-500">
                <div class="text-2xl mb-1">✅</div>
                <div class="text-2xl font-bold text-purple-600">{{ $stats['acknowledgment_rate'] ?? 0 }}%</div>
                <div class="text-xs text-gray-500">Acknowledgment Rate</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-orange-500">
                <div class="text-2xl mb-1">📊</div>
                <div class="text-2xl font-bold text-orange-600">{{ $stats['active_staff'] ?? 0 }}</div>
                <div class="text-xs text-gray-500">Active Staff</div>
            </div>
        </div>

        <!-- Staff Performance Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-800">
                <h2 class="text-lg font-bold text-white">Staff Performance</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acknowledged</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Memos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Acknowledged</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($staffPerformance as $performance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="{{ $performance['user']->profile_photo_url }}" class="h-8 w-8 rounded-full object-cover">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $performance['user']->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $performance['user']->position ?? 'Staff' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $performance['user']->department->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $performance['acknowledged'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $performance['total_memos'] }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 rounded-full h-2" style="width: {{ $performance['rate'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium 
                                            @if($performance['rate'] >= 80) text-green-600
                                            @elseif($performance['rate'] >= 50) text-yellow-600
                                            @else text-red-600 @endif">
                                            {{ $performance['rate'] }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $performance['last_acknowledged']?->diffForHumans() ?? 'Never' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No staff data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>