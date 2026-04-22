<?php

namespace App\Livewire\Memos;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Memo;
use App\Models\Department;
use App\Models\User;

class EditMemo extends Component
{
    use WithFileUploads;

    public Memo $memo;
    public $title;
    public $content;
    public $priority;
    public $department_id;
    public $effective_date;
    public $expiry_date;
    public $recipient_type;
    public $selected_departments = [];
    public $selected_users = [];
    public $new_attachments = [];
    public $require_acknowledgment;
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string|min:10',
        'priority' => 'required|in:low,medium,high,urgent',
        'effective_date' => 'required|date',
        'expiry_date' => 'nullable|date|after:effective_date',
        'recipient_type' => 'required|in:all,departments,specific_users',
        'selected_departments' => 'required_if:recipient_type,departments|array',
        'selected_users' => 'required_if:recipient_type,specific_users|array',
        'new_attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,png',
    ];

    public function mount(Memo $memo)
    {
        // Check permission - only creator or admin can edit
        if (auth()->id() !== $memo->created_by && !auth()->user()->isAdmin()) {
            abort(403, 'You cannot edit this memo.');
        }
        
        $this->memo = $memo;
        $this->title = $memo->title;
        $this->content = $memo->content;
        $this->priority = $memo->priority;
        $this->department_id = $memo->department_id;
        $this->effective_date = $memo->effective_date?->format('Y-m-d');
        $this->expiry_date = $memo->expiry_date?->format('Y-m-d');
        $this->recipient_type = $memo->recipient_type ?? 'all';
        $this->require_acknowledgment = $memo->require_acknowledgment;
        
        // Load existing recipients
        if ($this->recipient_type === 'departments' && $memo->recipients) {
            $this->selected_departments = collect($memo->recipients)
                ->where('type', 'department')
                ->pluck('id')
                ->toArray();
        } elseif ($this->recipient_type === 'specific_users' && $memo->recipients) {
            $this->selected_users = collect($memo->recipients)
                ->where('type', 'user')
                ->pluck('id')
                ->toArray();
        }
    }

    public function updatedRecipientType()
    {
        $this->selected_departments = [];
        $this->selected_users = [];
    }

    public function removeAttachment($index)
    {
        $attachments = $this->memo->attachments ?? [];
        if (isset($attachments[$index])) {
            // Delete file from storage
            \Storage::disk('public')->delete($attachments[$index]['path']);
            unset($attachments[$index]);
            $this->memo->update(['attachments' => array_values($attachments)]);
        }
    }

    public function removeNewAttachment($index)
    {
        unset($this->new_attachments[$index]);
        $this->new_attachments = array_values($this->new_attachments);
    }

    public function update()
    {
        $this->validate();
        
        // Save new attachments
        $existingAttachments = $this->memo->attachments ?? [];
        $newAttachments = [];
        
        foreach ($this->new_attachments as $attachment) {
            $path = $attachment->store('memo-attachments/' . date('Y/m'), 'public');
            $newAttachments[] = [
                'original_name' => $attachment->getClientOriginalName(),
                'path' => $path,
                'size' => $attachment->getSize(),
                'mime_type' => $attachment->getMimeType(),
            ];
        }
        
        // Merge attachments
        $allAttachments = array_merge($existingAttachments, $newAttachments);
        
        // Prepare recipients
        $recipients = [];
        if ($this->recipient_type === 'all') {
            $recipients = [['type' => 'all', 'id' => null]];
        } elseif ($this->recipient_type === 'departments') {
            foreach ($this->selected_departments as $deptId) {
                $recipients[] = ['type' => 'department', 'id' => $deptId];
            }
        } elseif ($this->recipient_type === 'specific_users') {
            foreach ($this->selected_users as $userId) {
                $recipients[] = ['type' => 'user', 'id' => $userId];
            }
        }
        
        // Update memo
        $this->memo->update([
            'title' => $this->title,
            'content' => $this->content,
            'priority' => $this->priority,
            'department_id' => $this->department_id,
            'effective_date' => $this->effective_date,
            'expiry_date' => $this->expiry_date,
            'recipients' => $recipients,
            'attachments' => $allAttachments,
            'require_acknowledgment' => $this->require_acknowledgment,
        ]);
        
        session()->flash('message', 'Memo updated successfully!');
        return redirect()->route('memos.show', $this->memo);
    }

    public function render()
    {
        return view('livewire.memos.edit-memo', [
            'departments' => Department::orderBy('name')->get(),
            'users' => User::with('department')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}