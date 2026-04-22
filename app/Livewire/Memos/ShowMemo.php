<?php

namespace App\Livewire\Memos;

use Livewire\Component;
use App\Models\Memo;
use App\Models\MemoAcknowledgment;

class ShowMemo extends Component
{
    public Memo $memo;
    public $hasAcknowledged = false;
    
    public function mount(Memo $memo)
    {
        $this->memo = $memo;
        $this->hasAcknowledged = $this->memo->acknowledgedBy(auth()->user());
    }
    
    public function acknowledge()
    {
        if (!$this->hasAcknowledged && $this->memo->require_acknowledgment) {
            MemoAcknowledgment::create([
                'memo_id' => $this->memo->id,
                'user_id' => auth()->id(),
                'acknowledged_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->hasAcknowledged = true;
            session()->flash('message', 'Memo acknowledged successfully!');
        }
    }
    
    public function render()
    {
        return view('livewire.memos.show-memo')->layout('layouts.app');
    }
}