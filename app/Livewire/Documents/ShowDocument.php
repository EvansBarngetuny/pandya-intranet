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
        $doc = $this->document;

        $candidates = [
            storage_path('app/public/' . $doc->file_path),
            storage_path('app/' . $doc->file_path),
            public_path('storage/' . $doc->file_path),
            public_path($doc->file_path),
        ];

        $filePath = null;
        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                $filePath = $candidate;
                break;
            }
        }
        if (!$filePath) {
            \Log::warning('Document download failed - file not found', ['document_id' => $doc->id, 'file_path' => $doc->file_path,'candidates' => $candidates]);
                    session()->flash('error', 'File not found on server. It may have been removed during a redeploy. Please contact IT to re-upload this document.');
            return null;        

        }

        $doc->incrementDownloadCount();
         // Also track in the audit trail
       $this->logActivity(
            'download_document',
            'documents',
            "Downloaded document: {$doc->title}",
            ['document_id' => $doc->id, 'file_name' => $doc->file_name]
    );

    $safeName = $this->sanitizeFilename($doc->file_name);

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
