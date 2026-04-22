<?php

namespace App\Livewire\Memos;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Memo;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Str;

class CreateMemo extends Component
{
    use WithFileUploads;

    public $memo_number;
    public $title;
    public $content;
    public $priority = 'medium';
    public $department_id;
    public $effective_date;
    public $expiry_date;
    public $recipient_type = 'all';
    public $selected_departments = [];
    public $selected_users = [];
    public $attachments = [];
    public $require_acknowledgment = true;

    protected $rules = [
        'memo_number' => 'required|unique:memos,memo_number',
        'title' => 'required|string|max:255',
        'content' => 'required|string|min:10',
        'priority' => 'required|in:low,medium,high,urgent',
        'effective_date' => 'required|date',
        'expiry_date' => 'nullable|date|after:effective_date',
        'recipient_type' => 'required|in:all,departments,specific_users',
        'selected_departments' => 'required_if:recipient_type,departments|array',
        'selected_users' => 'required_if:recipient_type,specific_users|array',
        'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,png',
    ];

    protected $messages = [
        'memo_number.unique' => 'This memo number already exists.',
        'selected_departments.required_if' => 'Please select at least one department.',
        'selected_users.required_if' => 'Please select at least one user.',
    ];

    public function mount()
    {
        $this->generateMemoNumber();
        $this->effective_date = date('Y-m-d');
    }

    public function generateMemoNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastMemo = Memo::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->orderBy('id', 'desc')
                        ->first();

        if ($lastMemo) {
            $lastNumber = intval(substr($lastMemo->memo_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $this->memo_number = "PMH/MEMO/{$year}{$month}/{$newNumber}";
    }

    public function updatedRecipientType()
    {
        $this->selected_departments = [];
        $this->selected_users = [];
    }

    public function saveAsDraft()
    {
        $this->validate();
        $this->saveMemo('draft');
        session()->flash('message', 'Memo saved as draft successfully!');
        return redirect()->route('memos.index');
    }

    public function publish()
    {
        $this->validate();
        $this->saveMemo('published');
        session()->flash('message', 'Memo published successfully!');
        return redirect()->route('memos.index');
    }
    protected function saveMemo($status)
{
    // Save attachments
    $savedAttachments = [];
    foreach ($this->attachments as $attachment) {
        $path = $attachment->store('memo-attachments/' . date('Y/m'), 'public');
        $savedAttachments[] = [
            'original_name' => $attachment->getClientOriginalName(),
            'path' => $path,
            'size' => $attachment->getSize(),
            'mime_type' => $attachment->getMimeType(),
        ];
    }

    // Prepare recipients array
    $recipients = $this->prepareRecipients();

    // Create memo with ALL required fields
    $memo = Memo::create([
        'memo_number' => $this->memo_number,
        'title' => $this->title,
        'content' => $this->content,
        'created_by' => auth()->id(),
        'department_id' => $this->department_id,
        'priority' => $this->priority,
        'effective_date' => $this->effective_date, // Add this - it was missing!
        'expiry_date' => $this->expiry_date,
        'attachments' => $savedAttachments,
        'recipients' => $recipients,
        'status' => $status,
        'require_acknowledgment' => $this->require_acknowledgment,
        'audience_type' => $this->recipient_type,
        'audience_ids' => $recipients,
        'published_at' => $status === 'published' ? now() : null,
    ]);

    return $memo;
}

    protected function prepareRecipients()
    {
        $recipients = [];

        if ($this->recipient_type === 'all') {
            $recipients[] = ['type' => 'all', 'id' => null];
        } elseif ($this->recipient_type === 'departments') {
            foreach ($this->selected_departments as $deptId) {
                $recipients[] = ['type' => 'department', 'id' => $deptId];
            }
        } elseif ($this->recipient_type === 'specific_users') {
            foreach ($this->selected_users as $userId) {
                $recipients[] = ['type' => 'user', 'id' => $userId];
            }
        }

        return $recipients;
    }

    public function render()
    {
        return view('livewire.memos.create-memo', [
            'departments' => Department::orderBy('name')->get(),
            'users' => User::with('department')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
