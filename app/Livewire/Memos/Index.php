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

    // Fixed: Single method for approval (keep this one, remove the other)
    public function approveMemo($memoId)
    {
        $memo = Memo::findOrFail($memoId);

        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'Only admins can approve memos.');
            return;
        }

        $memo->update([
            'status' => 'published',
            'published_at' => now(),
            'published_by' => auth()->id(),
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        session()->flash('message', 'Memo approved and published successfully!');
    }

    public function rejectMemo($memoId)
    {
        $memo = Memo::findOrFail($memoId);

        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'Only admins can reject memos.');
            return;
        }

        $memo->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        session()->flash('message', 'Memo rejected successfully!');
    }

    public function submitForApproval($memoId)
    {
        $memo = Memo::findOrFail($memoId);

        // Only the creator or HOD can submit for approval
        if (auth()->id() !== $memo->created_by && !auth()->user()->isHOD() && !auth()->user()->isAdmin()) {
            session()->flash('error', 'You cannot submit this memo for approval.');
            return;
        }

        $memo->update([
            'status' => Memo::STATUS_PENDING_APPROVAL,
        ]);

        session()->flash('message', 'Memo submitted for approval successfully!');
    }
// In the render method, add a flag for showing create button
public function render()
{
    $user = auth()->user();

    $memos = Memo::with('creator', 'readBy', 'acknowledgments')
        ->when(!$user->isAdmin(), function($query) use ($user) {
            // Non-admins (Staff and HOD) only see memos they have access to
            $query->where('status', Memo::STATUS_PUBLISHED)
                ->where(function($q) use ($user) {
                    $q->where('audience_type', 'all')
                        ->orWhere(function($q2) use ($user) {
                            $q2->where('audience_type', 'departments')
                                ->whereJsonContains('audience_ids', [['type' => 'department', 'id' => $user->department_id]]);
                        })
                        ->orWhere(function($q2) use ($user) {
                            $q2->where('audience_type', 'specific_users')
                                ->whereJsonContains('audience_ids', [['type' => 'user', 'id' => $user->id]]);
                        });
                });
        })
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
    $canCreateMemo = $user->isHOD() || $user->isAdmin();

    return view('livewire.memos.index', [
        'memos' => $memos,
        'departments' => $departments,
        'users' => $users,
        'canCreateMemo' => $canCreateMemo,
    ])->layout('layouts.app');
}

}
