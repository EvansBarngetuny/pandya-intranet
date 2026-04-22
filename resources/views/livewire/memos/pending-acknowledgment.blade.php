{{-- resources/views/livewire/memos/pending-acknowledgment.blade.php --}}
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-orange-500">
                <h1 class="text-2xl font-bold text-white">Pending Memo Acknowledgments</h1>
                <p class="text-yellow-100 text-sm mt-1">Please review and acknowledge the following memos</p>
            </div>

            <div class="p-6">
                @if (session()->has('message'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                @if(count($pendingMemos) > 0)
                    <div class="space-y-4">
                        @foreach($pendingMemos as $memo)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="font-bold text-lg">{{ $memo->title }}</h3>
                                            <span class="text-xs px-2 py-1 rounded-full
                                                @if($memo->priority == 'low') bg-green-100 text-green-700
                                                @elseif($memo->priority == 'medium') bg-yellow-100 text-yellow-700
                                                @elseif($memo->priority == 'high') bg-orange-100 text-orange-700
                                                @else bg-red-100 text-red-700 @endif">
                                                {{ ucfirst($memo->priority) }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($memo->content, 200) }}</p>
                                        
                                        <div class="text-xs text-gray-400 space-y-1">
                                            <p>Memo #: {{ $memo->memo_number }}</p>
                                            <p>Published: {{ $memo->published_at->format('F d, Y') }}</p>
                                            <p>From: {{ $memo->creator->name ?? 'Unknown' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="ml-4">
                                        <button wire:click="acknowledge({{ $memo->id }})"
                                                wire:confirm="By acknowledging, you confirm that you have read and understood this memo."
                                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                                            ✅ I Acknowledge
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">✅</div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">All Caught Up!</h3>
                        <p class="text-gray-500">You have no pending memos to acknowledge</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>