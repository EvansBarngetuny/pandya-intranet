<?php

namespace App\Livewire\Memos;

use Livewire\Component;
use App\Models\Memo;

class PendingAcknowledgment extends Component
{
    public $pendingMemos = [];
    
    public function mount()
    {
        $user = auth()->user();
        $this->pendingMemos = Memo::where('status', 'published')
            ->where('require_acknowledgment', true)
            ->whereDoesntHave('acknowledgments', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where(function($q) use ($user) {
                $q->where('audience_type', 'all')
                    ->orWhere(function($q2) use ($user) {
                        $q2->where('audience_type', 'departments')
                            ->whereJsonContains('audience_ids', $user->department_id);
                    })
                    ->orWhere(function($q2) use ($user) {
                        $q2->where('audience_type', 'specific_users')
                            ->whereJsonContains('audience_ids', $user->id);
                    });
            })
            ->orderBy('priority', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();
    }
    
    public function acknowledge($memoId)
    {
        $memo = Memo::findOrFail($memoId);
        
        if (!$memo->acknowledgedBy(auth()->user())) {
            \App\Models\MemoAcknowledgment::create([
                'memo_id' => $memoId,
                'user_id' => auth()->id(),
                'acknowledged_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            session()->flash('message', 'Memo acknowledged successfully!');
            $this->mount(); // Refresh the list
        }
    }
    
    public function render()
    {
        return view('livewire.memos.pending-acknowledgment')->layout('layouts.app');
    }
}