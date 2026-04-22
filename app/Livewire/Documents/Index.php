<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $showUploadForm = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        
        // Increment download count
        $document->increment('download_count');
        
        // Log download activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'download_document',
            'module' => 'documents',
            'description' => "Downloaded document: {$document->title}",
            'ip_address' => request()->ip(),
        ]);
        
        return response()->download(storage_path('app/public/' . $document->file_path), $document->file_name);
    }

    public function deleteDocument($id)
    {
        $document = Document::findOrFail($id);
        
        // Check permission
        if (!auth()->user()->isAdmin() && $document->uploaded_by !== auth()->id()) {
            session()->flash('error', 'You cannot delete this document.');
            return;
        }
        
        // Delete file from storage
        \Storage::disk('public')->delete($document->file_path);
        
        // Delete record
        $document->delete();
        
        session()->flash('message', 'Document deleted successfully.');
    }

    public function render()
    {
        $documents = Document::with('uploader')
            ->where('is_active', true)
            ->when($this->search, function($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->category, fn($q) => $q->where('category', $this->category))
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = [
            'sop' => 'Standard Operating Procedures',
            'policy' => 'Hospital Policies',
            'form' => 'Forms & Templates',
            'guideline' => 'Clinical Guidelines',
            'manual' => 'Staff Manuals',
        ];

        return view('livewire.documents.index', [
            'documents' => $documents,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}