{{-- resources/views/livewire/admin/reports.blade.php --}}
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📊 System Reports</h1>
                    <p class="text-gray-500 mt-1">Comprehensive analytics and insights for hospital operations</p>
                </div>
                <div class="flex gap-2">
                    <span class="text-sm text-gray-400">Last updated: {{ now()->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Staff</p>
                        <p class="text-3xl font-bold">{{ $staffStats['total'] }}</p>
                    </div>
                    <div class="text-4xl">👥</div>
                </div>
                <div class="mt-2">
                    <span class="text-xs text-blue-200">+{{ $staffStats['new_this_month'] }} this month</span>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Total Memos</p>
                        <p class="text-3xl font-bold">{{ $memoStats['total'] }}</p>
                    </div>
                    <div class="text-4xl">📄</div>
                </div>
                <div class="mt-2">
                    <span class="text-xs text-green-200">{{ $memoStats['published'] }} published</span>
                </div>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Total Documents</p>
                        <p class="text-3xl font-bold">{{ $documentStats['total'] }}</p>
                    </div>
                    <div class="text-4xl">📚</div>
                </div>
                <div class="mt-2">
                    <span class="text-xs text-purple-200">{{ $documentStats['total_downloads'] }} downloads</span>
                </div>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm">Acknowledgment Rate</p>
                        <p class="text-3xl font-bold">{{ $memoStats['acknowledgment_rate'] }}%</p>
                    </div>
                    <div class="text-4xl">✅</div>
                </div>
                <div class="mt-2">
                    <span class="text-xs text-orange-200">Overall compliance</span>
                </div>
            </div>
        </div>

        <!-- Staff Statistics Card -->
        <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">👥 Staff Demographics</h2>
                        <p class="text-sm text-gray-500">Department and role distribution</p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="exportStaffReportPDF"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            PDF
                        </button>
                        <button wire:click="exportStaffReportExcel"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Excel
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Department Distribution -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Staff by Department</h3>
                        <div class="space-y-3">
                            @foreach($staffStats['by_department'] as $dept)
                                @php $percentage = ($dept->count / max($staffStats['total'], 1)) * 100; @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">{{ $dept->department->name ?? 'Unassigned' }}</span>
                                        <span class="font-medium text-gray-800">{{ $dept->count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 rounded-full h-2 transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Role Distribution -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Staff by Role</h3>
                        <div class="space-y-3">
                            @foreach($staffStats['by_role'] as $role)
                                @php
                                    $percentage = ($role->count / max($staffStats['total'], 1)) * 100;
                                    $colors = ['admin' => 'purple', 'hod' => 'blue', 'staff' => 'green'];
                                    $color = $colors[$role->role] ?? 'gray';
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600 capitalize">{{ $role->role }}</span>
                                        <span class="font-medium text-gray-800">{{ $role->count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $color }}-500 rounded-full h-2 transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Memo Statistics Card -->
        <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">📄 Memo Analytics</h2>
                        <p class="text-sm text-gray-500">Distribution and acknowledgment tracking</p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="exportMemoReportPDF"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            PDF
                        </button>
                        <button wire:click="exportMemoReportExcel"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Excel
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Priority Distribution -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Memos by Priority</h3>
                        <div class="space-y-3">
                            @foreach($memoStats['by_priority'] as $priority)
                                @php
                                    $percentage = ($priority->count / max($memoStats['total'], 1)) * 100;
                                    $colors = ['urgent' => 'red', 'high' => 'orange', 'medium' => 'yellow', 'low' => 'green'];
                                    $color = $colors[$priority->priority] ?? 'gray';
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600 capitalize">{{ $priority->priority }}</span>
                                        <span class="font-medium text-gray-800">{{ $priority->count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $color }}-500 rounded-full h-2 transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Recent Memos -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Recent Memos</h3>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($memoStats['recent_memos'] as $memo)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">{{ Str::limit($memo->title, 40) }}</p>
                                        <p class="text-xs text-gray-500">{{ $memo->memo_number }} • {{ $memo->created_at->diffForHumans() }}</p>
                                    </div>
                                    <button wire:click="viewMemoAudit({{ $memo->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            <span class="sm:inline">Audit →</span>
    <span class="sm:hidden">Audit →</span>


                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Statistics Card -->
        <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">📚 Document Repository</h2>
                        <p class="text-sm text-gray-500">Inventory and usage metrics</p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="exportDocumentReportPDF"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            PDF
                        </button>
                        <button wire:click="exportDocumentReportExcel"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Excel
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Category Distribution -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Documents by Category</h3>
                        <div class="space-y-3">
                            @foreach($documentStats['by_category'] as $category)
                                @php
                                    $percentage = ($category->count / max($documentStats['total'], 1)) * 100;
                                    $icons = ['sop' => '📋', 'policy' => '📜', 'form' => '📝', 'guideline' => '📖', 'manual' => '📘'];
                                    $icon = $icons[$category->category] ?? '📄';
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">{{ $icon }} {{ ucfirst(str_replace('_', ' ', $category->category)) }}</span>
                                        <span class="font-medium text-gray-800">{{ $category->count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-teal-500 rounded-full h-2 transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Recent Documents -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Recently Added Documents</h3>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($documentStats['recent_documents'] as $doc)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">{{ Str::limit($doc->title, 40) }}</p>
                                        <p class="text-xs text-gray-500">{{ $doc->file_name }} • {{ $doc->created_at->diffForHumans() }}</p>
                                    </div>
                                    <button wire:click="viewDocumentAudit({{ $doc->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Audit →
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Memo Audit Trail Modal -->
@if($showMemoAudit && $selectedMemo)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
     x-data="{ open: true }" x-show="open" x-on:click.away="open = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="p-4 border-b bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold">📄 Memo Audit Trail</h3>
            <p class="text-sm text-blue-100">{{ $selectedMemo->title }} ({{ $selectedMemo->memo_number }})</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportMemoAuditPDF" 
                    wire:loading.attr="disabled"
                    class="bg-blue-500 hover:bg-blue-400 text-white px-3 py-1.5 rounded text-sm transition flex items-center gap-1 border border-blue-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span class="hidden sm:inline">PDF</span>
                <span class="sm:hidden">PDF</span>
            </button>
            <button wire:click="exportMemoAuditExcel" 
                    wire:loading.attr="disabled"
                    class="bg-green-600 hover:bg-green-500 text-white px-3 py-1.5 rounded text-sm transition flex items-center gap-1 border border-green-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Excel</span>
                <span class="sm:hidden">Excel</span>
            </button>
            <button wire:click="closeMemoAudit" 
                    class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-1.5 rounded text-sm transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="hidden sm:inline">Close</span>
                <span class="sm:hidden">✕</span>
            </button>
        </div>
    </div>
</div>
        
        <!-- Filter Section -->
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                    <select wire:model.live="memoAuditDepartmentFilter" 
                            wire:change="applyMemoAuditFilters"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Staff Member</label>
                    <select wire:model.live="memoAuditUserFilter" 
                            wire:change="applyMemoAuditFilters"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Staff</option>
                        @foreach($memoAuditUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->department->name ?? 'No Dept' }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="memoAuditStatusFilter" 
                            wire:change="applyMemoAuditFilters"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="read">Read</option>
                        <option value="unread">Not Read</option>
                        <option value="acknowledged">Acknowledged</option>
                        <option value="pending">Pending Acknowledgment</option>
                    </select>
                </div>
                
                <div>
                    <button wire:click="resetMemoFilters" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </button>
                </div>
            </div>
            
            <!-- Active Filters Display -->
            @if($memoAuditDepartmentFilter || $memoAuditUserFilter || $memoAuditStatusFilter)
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="text-xs text-gray-600">Active filters:</span>
                @if($memoAuditDepartmentFilter)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        Dept: {{ $departments->firstWhere('id', $memoAuditDepartmentFilter)->name ?? 'Unknown' }}
                        <button wire:click="$set('memoAuditDepartmentFilter', '')" class="ml-1 hover:text-blue-600">×</button>
                    </span>
                @endif
                @if($memoAuditUserFilter)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                        Staff: {{ $memoAuditUsers->firstWhere('id', $memoAuditUserFilter)->name ?? 'Unknown' }}
                        <button wire:click="$set('memoAuditUserFilter', '')" class="ml-1 hover:text-green-600">×</button>
                    </span>
                @endif
                @if($memoAuditStatusFilter)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                        Status: {{ ucfirst($memoAuditStatusFilter) }}
                        <button wire:click="$set('memoAuditStatusFilter', '')" class="ml-1 hover:text-purple-600">×</button>
                    </span>
                @endif
            </div>
            @endif
        </div>
        
        <!-- Modal Body with Paginated Table -->
        <div class="overflow-x-auto p-4 max-h-[calc(90vh-240px)]">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Read</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Read At</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acknowledged</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acknowledged At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($paginatedMemoAudit as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            <div class="flex items-center gap-2">
                                <img src="{{ $record['user']->profile_photo_url ?? 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name=' . urlencode($record['user']->name) }}" 
                                     class="h-6 w-6 rounded-full object-cover">
                                {{ $record['user']->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $record['user']->department->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            @if($record['read_at'])
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">✓ Read</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">✗ Not Read</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $record['read_at'] ? \Carbon\Carbon::parse($record['read_at'])->format('M d, Y h:i A') : '-' }}</td>
                        <td class="px-4 py-3">
                            @if($record['acknowledged_at'])
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">✓ Acknowledged</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">⏳ Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('M d, Y h:i A') : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No audit records found with selected filters</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination and Stats Footer -->
        <div class="p-3 border-t bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-700">Show</span>
                    <select wire:model.live="memoAuditPerPage" 
                            class="border border-gray-300 rounded-md text-sm py-1 px-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-700">entries</span>
                </div>
                
                <div class="flex flex-wrap gap-4 text-sm">
                    <span class="text-gray-600">Total: <strong>{{ $memoAuditTrail->count() }}</strong></span>
                    <span class="text-gray-600">Read: <strong class="text-green-600">{{ $memoAuditTrail->where('read_at', '!=', null)->count() }}</strong></span>
                    <span class="text-gray-600">Unread: <strong class="text-red-600">{{ $memoAuditTrail->where('read_at', '==', null)->count() }}</strong></span>
                    <span class="text-gray-600">Acknowledged: <strong class="text-blue-600">{{ $memoAuditTrail->where('acknowledged_at', '!=', null)->count() }}</strong></span>
                    <span class="text-gray-600">Pending: <strong class="text-yellow-600">{{ $memoAuditTrail->where('acknowledged_at', '==', null)->count() }}</strong></span>
                </div>
                
                <div>
                    {{ $paginatedMemoAudit->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif

        <!-- Document Audit Trail Modal -->
        <!-- Document Audit Trail Modal -->
@if($showDocumentAudit && $selectedDocument)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     x-data="{ open: true }" x-show="open" x-on:click.away="open = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
       <div class="p-4 border-b bg-blue-600 text-white">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold">📚 Document Audit Trail</h3>
            <p class="text-sm text-blue-100">{{ $selectedDocument->title }}</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportDocumentAuditPDF" 
                    wire:loading.attr="disabled"
                    class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-sm transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span class="hidden sm:inline">PDF</span>
            </button>
            <button wire:click="exportDocumentAuditExcel" 
                    wire:loading.attr="disabled"
                    class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-sm transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Excel</span>
            </button>
            <button wire:click="closeDocumentAudit" class="text-white hover:text-gray-200 text-2xl leading-none">×</button>
        </div>
    </div>
</div>
        
        <!-- Filter Section for Documents -->
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                    <select wire:model.live="documentAuditDepartmentFilter" 
                            wire:change="applyDocumentAuditFilters"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Staff Member</label>
                    <select wire:model.live="documentAuditUserFilter" 
                            wire:change="applyDocumentAuditFilters"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                        <option value="">All Staff</option>
                        @foreach($documentAuditUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->department->name ?? 'No Dept' }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="documentAuditStatusFilter" 
                            wire:change="applyDocumentAuditFilters"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                        <option value="">All Status</option>
                        <option value="viewed">Viewed</option>
                        <option value="not_viewed">Not Viewed</option>
                        <option value="acknowledged">Acknowledged</option>
                        <option value="pending">Pending Acknowledgment</option>
                    </select>
                </div>
                
                <div>
                    <button wire:click="resetDocumentFilters" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </button>
                </div>
            </div>
            
            <!-- Active Filters Display -->
            @if($documentAuditDepartmentFilter || $documentAuditUserFilter || $documentAuditStatusFilter)
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="text-xs text-gray-600">Active filters:</span>
                @if($documentAuditDepartmentFilter)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-teal-100 text-teal-800">
                        Dept: {{ $departments->firstWhere('id', $documentAuditDepartmentFilter)->name ?? 'Unknown' }}
                        <button wire:click="$set('documentAuditDepartmentFilter', '')" class="ml-1 hover:text-teal-600">×</button>
                    </span>
                @endif
                @if($documentAuditUserFilter)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                        Staff: {{ $documentAuditUsers->firstWhere('id', $documentAuditUserFilter)->name ?? 'Unknown' }}
                        <button wire:click="$set('documentAuditUserFilter', '')" class="ml-1 hover:text-green-600">×</button>
                    </span>
                @endif
                @if($documentAuditStatusFilter)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                        Status: {{ ucfirst(str_replace('_', ' ', $documentAuditStatusFilter)) }}
                        <button wire:click="$set('documentAuditStatusFilter', '')" class="ml-1 hover:text-purple-600">×</button>
                    </span>
                @endif
            </div>
            @endif
        </div>
        
        <!-- Document Audit Table with Pagination -->
        <div class="overflow-x-auto p-4 max-h-[calc(90vh-240px)]">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Viewed</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Viewed At</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acknowledged</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acknowledged At</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Downloaded</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($paginatedDocumentAudit as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            <div class="flex items-center gap-2">
                                <img src="{{ $record['user']->profile_photo_url ?? 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name=' . urlencode($record['user']->name) }}" 
                                     class="h-6 w-6 rounded-full object-cover">
                                {{ $record['user']->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $record['user']->department->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            @if($record['viewed_at'])
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">✓ Viewed</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">✗ Not Viewed</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $record['viewed_at'] ? \Carbon\Carbon::parse($record['viewed_at'])->format('M d, Y h:i A') : '-' }}</td>
                        <td class="px-4 py-3">
                            @if($record['acknowledged_at'])
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">✓ Acknowledged</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">⏳ Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('M d, Y h:i A') : '-' }}</td>
                        <td class="px-4 py-3">
                            @if($record['downloaded'])
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">📥 Yes</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No audit records found with selected filters</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination and Stats Footer -->
        <div class="p-3 border-t bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-700">Show</span>
                    <select wire:model.live="documentAuditPerPage" 
                            class="border border-gray-300 rounded-md text-sm py-1 px-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-700">entries</span>
                </div>
                
                <div class="flex flex-wrap gap-4 text-sm">
                    <span class="text-gray-600">Total: <strong>{{ $documentAuditTrail->count() }}</strong></span>
                    <span class="text-gray-600">Viewed: <strong class="text-green-600">{{ $documentAuditTrail->where('viewed_at', '!=', null)->count() }}</strong></span>
                    <span class="text-gray-600">Not Viewed: <strong class="text-red-600">{{ $documentAuditTrail->where('viewed_at', '==', null)->count() }}</strong></span>
                    <span class="text-gray-600">Acknowledged: <strong class="text-blue-600">{{ $documentAuditTrail->where('acknowledged_at', '!=', null)->count() }}</strong></span>
                    <span class="text-gray-600">Pending: <strong class="text-yellow-600">{{ $documentAuditTrail->where('acknowledged_at', '==', null)->count() }}</strong></span>
                </div>
                
                <div>
                    {{ $paginatedDocumentAudit->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
    </div>
</div>

@push('styles')
<style>
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
    .sticky {
        position: sticky;
    }
    .top-0 {
        top: 0;
    }
</style>
@endpush
