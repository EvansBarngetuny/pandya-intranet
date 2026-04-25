<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Document;

class CreateDocument extends Component
{
    use WithFileUploads;

    public $title;
    public $description;
    public $category = 'policy';
    public $file;
    public $version = 1;
    public $effective_date;
    public $is_active = true;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category' => 'required|in:sop,policy,form,guideline,manual',
        'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
        'version' => 'required|integer|min:1',
        'effective_date' => 'nullable|date',
    ];

    protected $messages = [
        'file.required' => 'Please select a file to upload.',
        'file.max' => 'File size cannot exceed 10MB.',
        'file.mimes' => 'File must be a PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, or TXT file.',
    ];

    public function save()
    {
        $this->validate();

        // Store file
        $path = $this->file->store('documents/' . $this->category, 'public');

        // Create document record with ALL required fields
        Document::create([
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'file_path' => $path,
            'file_name' => $this->file->getClientOriginalName(), // REQUIRED
            'file_size' => $this->formatBytes($this->file->getSize()), // REQUIRED
            'file_type' => $this->file->getMimeType(), // REQUIRED
            'uploaded_by' => auth()->id(),
            'download_count' => 0, // REQUIRED (default 0)
            'version' => $this->version, // REQUIRED (default 1)
            'effective_date' => $this->effective_date,
            'is_active' => $this->is_active, // REQUIRED (default 1)
            'accessible_roles' => null, // Optional
        ]);

        session()->flash('message', 'Document uploaded successfully!');
        return redirect()->route('documents.index');
    }

    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function render()
    {
        return view('livewire.documents.create-document')->layout('layouts.app');
    }
}
