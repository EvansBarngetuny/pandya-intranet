<?php

namespace App\Livewire\Memos;

use App\Models\Department;
use App\Models\Memo;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $priority = '';
    public $status = '';
    public $showCreateForm = false;
    public $showEditForm = false;
    public $selectedMemo = null;

    // Form fields
    public $memo_number;
    public $title;
    public $content;
    public $department_id;
    public $effective_date;
    public $expiry_date;
    public $recipients = [];
    public $recipient_type = 'all';
    public $selected_departments = [];
    public $selected_users = [];

    protected $rules = [
        'memo_number' => 'required|unique:memos',
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'priority' => 'required|in:low,medium,high,urgent',
        'effective_date' => 'required|date',
        'expiry_date' => 'nullable|date|after:effective_date',
    ];

    public function createMemo()
    {
        $this->validate();

        $recipients = [];
        if ($this->recipient_type === 'all') {
            $recipients = [['type' => 'all']];
        } elseif ($this->recipient_type === 'departments') {
            foreach ($this->selected_departments as $deptId) {
                $recipients[] = ['type' => 'department', 'id' => $deptId];
            }
        } elseif ($this->recipient_type === 'users') {
            foreach ($this->selected_users as $userId) {
                $recipients[] = ['type' => 'user', 'id' => $userId];
            }
        }

        $memo = Memo::create([
            'memo_number' => $this->memo_number,
            'title' => $this->title,
            'content' => $this->content,
            'created_by' => auth()->id(),
            'department_id' => $this->department_id,
            'priority' => $this->priority,
            'effective_date' => $this->effective_date,
            'expiry_date' => $this->expiry_date,
            'recipients' => $recipients,
            'status' => 'draft'
        ]);

        session()->flash('message', 'Memo created successfully!');
        $this->reset(['showCreateForm', 'memo_number', 'title', 'content', 'priority', 'effective_date', 'expiry_date']);
        $this->dispatch('memo-created');
    }

    public function publishMemo($memoId)
    {
        $memo = Memo::findOrFail($memoId);
        $memo->update([
            'status' => 'published',
            'published_at' => now()
        ]);
        session()->flash('message', 'Memo published successfully!');
    }

   public function markAsRead($memoId)
{
    $alreadyRead = \DB::table('memo_reads')
        ->where('memo_id', $memoId)
        ->where('user_id', auth()->id())
        ->exists();

    if (!$alreadyRead) {
        \DB::table('memo_reads')->insert([
            'memo_id' => $memoId,
            'user_id' => auth()->id(),
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->flash('message', 'Memo marked as read!');
    }

    $this->dispatch('memo-read');
}

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPriority()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $memos = Memo::with('creator', 'readBy')
            ->when($this->search, function($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('memo_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->priority, fn($q) => $q->where('priority', $this->priority))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $departments = Department::all();
        $users = User::all();
        // Update the markAsRead method in app/Livewire/Memos/Index.php

        return view('livewire.memos.index', [
            'memos' => $memos,
            'departments' => $departments,
            'users' => $users
        ])->layout('layouts.app');
    }
}
