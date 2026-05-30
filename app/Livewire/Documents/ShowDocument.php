<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use App\Traits\LogsActivity;
use App\Models\Document;
use App\Models\User;

class ShowDocument extends Component
{
    use LogsActivity;
    public Document $document;
    public $hasViewed = false;
    public $hasAcknowledged = false;
    public $showAuditTrail = false;
    public $viewedAt = null;
    public $acknowledgedAt = null;
    public $auditTrail = [];

    public function mount(Document $document)
    {
        $this->document = $document;
        //check if the user has viewed
        $this->hasViewed = $this->document->viewedBy(auth()->user());
        $this->hasAcknowledged = $this->document->acknowledgedBy(auth()->user());

        // Get timestamps

        $view = $this->document->views()->where('user_id', auth()->id())->first();
        $this->viewedAt = $view?->viewed_at;

        $ack = $this->document->acknowledgments()->where('user_id', auth()->id())->first();
        $this->acknowledgedAt = $ack?->acknowledged_at;

        //Auto-Mark as Viewed
        if (!$this->hasViewed) {
            $this->document->markAsViewed(auth()->user());
            $this->hasViewed = true;
            $this->viewedAt = now();
        }
        //Load filtered audit trail for for HOD/Admins
        if (auth()->user()->isAdmin() || auth()->user()->isHOD()) {
            $this->loadAuditTrail();
        }

        // Log view
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'view_document',
            'module' => 'documents',
            'description' => "Viewed document: {$document->title}",
            'ip_address' => request()->ip(),
        ]);
    }
    protected function loadAuditTrail()
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            $user = User::where('is_active', true)->get();
        }
        else {
            // For HODs, only show staff in their department
            $user = User::where('department_id', $user->department_id)
                        ->where('is_active', true)
                        ->get();
        }
        $auditTrail = [];
        foreach ($user as $staff) {
            $auditTrail[] = [
                'user' => $staff,
                'viewed_at' => $this->document->views()->where('user_id', $staff->id)->first()?->viewed_at,
                 'acknowledged_at' => $this->document->acknowledgments()->where('user_id', $staff->id)->first()?->acknowledged_at,
                 'downloaded' => $this->document->downloads()->where('user_id', $staff->id)->exists(),
            ];
        }
        $this->auditTrail = $auditTrail;
    }
    public function getAuditTrailProperty()
    {
        return $this->auditTrail;
    }

    private function sanitizeFilename($filename)
{
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    $filename = preg_replace('/_+/', '_', $filename);
    $filename = trim($filename, '_');
    return substr($filename, 0, 200);
}
    public function download()
    {
        $this->document->incrementDownloadCount();


         // Also track in the audit trail
       $this->ActivityLog(
            'download_document',
            'document',
            "Downloaded document: {$this->document->title}",
            ['document_id' => $this->document->id, 'file_name' => $this->document->file_name]
    );

    // Check if file exists
    $filePath = storage_path('app/public/' . $this->document->file_path);
    $safeName = $this->sanitizeFilename($this->document->file_name);

    return response()->download($filePath, $safeName);
    }

      public function acknowledge()
    {
        if (!$this->hasAcknowledged && $this->document->require_acknowledgment) {
            $this->document->acknowledge(auth()->user());
            $this->hasAcknowledged = true;
            $this->acknowledgedAt = now();

            $this->logActivity(
                'acknowledge_document',
                'document',
                "Acknowledged document: {$this->document->title}",
            ['document_id' => $this->document->id, 'file_name' => $this->document->file_name]);

            session()->flash('message', 'Document acknowledged successfully! This serves as your digital signature.');
        }
    }
    public function toggleAuditTrail()
    {
        $this->showAuditTrail = !$this->showAuditTrail;
        if ($this->showAuditTrail) {
            $this->loadAuditTrail();
        }
    }

    public function render()
    {
        return view('livewire.documents.show-document',
        [
            'viewedAt' => $this->viewedAt,
            'acknowledgedAt' => $this->acknowledgedAt,
            'auditTrail' => $this->auditTrail,
        ])->layout('layouts.app');
    }
}
