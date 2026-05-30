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
                <div class="text-xs text-gray-500">Department Memos</div>
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

        <!-- Recent Memos -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-800">
                <h2 class="text-lg font-bold text-white">Recent Department Memos</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Memo Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Memo #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Published</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($memoStats['recent_memos'] as $memo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($memo->title, 40) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $memo->memo_number }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($memo->priority == 'urgent') bg-red-100 text-red-700
                                        @elseif($memo->priority == 'high') bg-orange-100 text-orange-700
                                        @elseif($memo->priority == 'medium') bg-yellow-100 text-yellow-700
                                        @else bg-green-100 text-green-700 @endif">
                                        {{ ucfirst($memo->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $memo->published_at?->format('M d, Y') ?? 'Draft' }}</td>
                                <td class="px-6 py-4">
                                    <button wire:click="viewMemoAudit({{ $memo->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View Staff Audit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No memos found for your department</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Documents -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
                <h2 class="text-lg font-bold">Recent Documents</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Version</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uploaded</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($documentStats['recent_documents'] as $doc)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($doc->title, 50) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($doc->category == 'sop') bg-purple-100 text-purple-700
                                        @elseif($doc->category == 'policy') bg-blue-100 text-blue-700
                                        @elseif($doc->category == 'form') bg-green-100 text-green-700
                                        @elseif($doc->category == 'guideline') bg-yellow-100 text-yellow-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ strtoupper(str_replace('_', ' ', $doc->category)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">v{{ $doc->version }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $doc->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <button wire:click="viewDocumentAudit({{ $doc->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium text-sm font-medium">
                                        View Staff Audit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No documents found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Memos Ack</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Memos Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Memo Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doc Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Activity</th>
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
                                            <p class="text-xs text-gray-500">{{ $performance['user']->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $performance['user']->position ?? 'Staff' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $performance['acknowledged'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $performance['total_memos'] }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 rounded-full h-2" style="width: {{ $performance['memo_rate'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium
                                            @if($performance['memo_rate'] >= 80) text-green-600
                                            @elseif($performance['memo_rate'] >= 50) text-yellow-600
                                            @else text-red-600 @endif">
                                            {{ $performance['memo_rate'] }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 rounded-full h-2" style="width: {{ $performance['document_rate'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-blue-600">
                                            {{ $performance['document_rate'] }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $performance['last_acknowledged']?->diffForHumans() ?? 'Never' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">No staff data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Memo Audit Trail Modal -->
        @if($showMemoAudit && $selectedMemo)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                 x-data="{ open: true }" x-show="open" x-on:click.away="open = false">
                <div class="bg-white rounded-xl shadow-2xl max-w-5xl w-full mx-4 max-h-[85vh] overflow-hidden">
                    <div class="p-4 border-b bg-gradient-to-r from-green-600 to-green-800 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold">Memo Audit Trail - {{ $departmentName }}</h3>
                                <p class="text-sm">{{ $selectedMemo->title }} ({{ $selectedMemo->memo_number }})</p>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="exportMemoAuditPDF" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-sm">📄 PDF</button>
                                <button wire:click="exportMemoAuditExcel" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-sm">📊 Excel</button>
                                <button wire:click="closeMemoAudit" class="text-white hover:text-gray-200 text-2xl leading-none">×</button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr><th>Staff Name</th><th>Department</th><th>Read Status</th><th>Read At</th><th>Acknowledged</th><th>Acknowledged At</th></tr>
                            </thead>
                            <tbody>
                                @foreach($memoAuditTrail as $record)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">{{ $record['user']->name }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $record['user']->department->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">{{ $record['read_at'] ? 'Read' : 'Not Read' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $record['read_at'] ? \Carbon\Carbon::parse($record['read_at'])->format('M d, Y h:i A') : '-' }}</td>
                                        <td class="px-4 py-3">{{ $record['acknowledged_at'] ? 'Acknowledged' : 'Pending' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('M d, Y h:i A') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Document Audit Trail Modal -->
        @if($showDocumentAudit && $selectedDocument)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                 x-data="{ open: true }" x-show="open" x-on:click.away="open = false">
                <div class="bg-white rounded-xl shadow-2xl max-w-5xl w-full mx-4 max-h-[85vh] overflow-hidden">
                    <div class="p-4 border-b bg-gradient-to-r from-blue-600 to-blue-800 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold">Document Audit Trail - {{ $departmentName }}</h3>
                                <p class="text-sm">{{ $selectedDocument->title }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="exportDocumentAuditPDF" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-sm">📄 PDF</button>
                                <button wire:click="exportDocumentAuditExcel" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-sm">📊 Excel</button>
                                <button wire:click="closeDocumentAudit" class="text-white hover:text-gray-200 text-2xl leading-none">×</button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <td><th>Staff Name</th><th>Department</th><th>Viewed</th><th>Viewed At</th><th>Acknowledged</th><th>Acknowledged At</th><th>Downloaded</th></tr>
                            </thead>
                            <tbody>
                                @foreach($documentAuditTrail as $record)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">{{ $record['user']->name }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $record['user']->department->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">{{ $record['viewed_at'] ? 'Yes' : 'No' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $record['viewed_at'] ? \Carbon\Carbon::parse($record['viewed_at'])->format('M d, Y h:i A') : '-' }}</td>
                                        <td class="px-4 py-3">{{ $record['acknowledged_at'] ? 'Yes' : 'No' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $record['acknowledged_at'] ? \Carbon\Carbon::parse($record['acknowledged_at'])->format('M d, Y h:i A') : '-' }}</td>
                                        <td class="px-4 py-3">{{ $record['downloaded'] ? 'Yes' : 'No' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
