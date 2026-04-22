<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use App\Models\Document;

class ShowDocument extends Component
{
    public Document $document;

    public function mount(Document $document)
    {
        $this->document = $document;
        
        // Log view
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'view_document',
            'module' => 'documents',
            'description' => "Viewed document: {$document->title}",
            'ip_address' => request()->ip(),
        ]);
    }

    public function download()
    {
        $this->document->increment('download_count');
        
        return response()->download(storage_path('app/public/' . $this->document->file_path), $this->document->file_name);
    }

    public function render()
    {
        return view('livewire.documents.show-document')->layout('layouts.app');
    }
}