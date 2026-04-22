{{-- resources/views/livewire/admin/reports.blade.php --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">System Reports</h1>
            <p class="text-gray-600 mt-1">Analytics and insights for hospital operations</p>
        </div>

        <!-- Staff Statistics -->
        <div class="bg-white rounded-lg shadow-lg mb-6 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800">
                <h2 class="text-xl font-bold text-white">Staff Statistics</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600">{{ $staffStats['total'] }}</div>
                        <div class="text-sm text-gray-600">Total Staff</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-3xl font-bold text-green-600">{{ $staffStats['new_this_month'] }}</div>
                        <div class="text-sm text-gray-600">New This Month</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-3xl font-bold text-purple-600">{{ $staffStats['by_role']->where('role', 'admin')->first()?->count ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Administrators</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-3">Staff by Department</h3>
                        <div class="space-y-2">
                            @foreach($staffStats['by_department'] as $dept)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">{{ $dept->department->name ?? 'No Department' }}</span>
                                    <span class="text-sm font-semibold">{{ $dept->count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 rounded-full h-2" style="width: {{ ($dept->count / $staffStats['total']) * 100 }}%"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-3">Staff by Role</h3>
                        <div class="space-y-2">
                            @foreach($staffStats['by_role'] as $role)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">{{ ucfirst($role->role) }}</span>
                                    <span class="text-sm font-semibold">{{ $role->count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 rounded-full h-2" style="width: {{ ($role->count / $staffStats['total']) * 100 }}%"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Memo Statistics -->
        <div class="bg-white rounded-lg shadow-lg mb-6 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-800">
                <h2 class="text-xl font-bold text-white">Memo Statistics</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $memoStats['total'] }}</div>
                        <div class="text-xs text-gray-600">Total Memos</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $memoStats['published'] }}</div>
                        <div class="text-xs text-gray-600">Published</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ $memoStats['acknowledgment_rate'] }}%</div>
                        <div class="text-xs text-gray-600">Acknowledgment Rate</div>
                    </div>
                </div>
                
                <h3 class="font-semibold text-gray-800 mb-3">Memos by Priority</h3>
                <div class="space-y-2">
                    @foreach($memoStats['by_priority'] as $priority)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ ucfirst($priority->priority) }}</span>
                            <span class="text-sm font-semibold">{{ $priority->count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 rounded-full h-2" style="width: {{ ($priority->count / $memoStats['total']) * 100 }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- News Statistics -->
        <div class="bg-white rounded-lg shadow-lg mb-6 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-800">
                <h2 class="text-xl font-bold text-white">News Statistics</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ $newsStats['total'] }}</div>
                        <div class="text-xs text-gray-600">Total News</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $newsStats['this_month'] }}</div>
                        <div class="text-xs text-gray-600">This Month</div>
                    </div>
                </div>
                
                <h3 class="font-semibold text-gray-800 mb-3">News by Category</h3>
                <div class="space-y-2">
                    @foreach($newsStats['by_category'] as $category)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ ucfirst($category->category) }}</span>
                            <span class="text-sm font-semibold">{{ $category->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Event Statistics -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-600 to-red-600">
                <h2 class="text-xl font-bold text-white">Event Statistics</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600">{{ $eventStats['total'] }}</div>
                        <div class="text-xs text-gray-600">Total Events</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $eventStats['upcoming'] }}</div>
                        <div class="text-xs text-gray-600">Upcoming</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>