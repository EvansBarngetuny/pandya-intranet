<?php

namespace App\Livewire\News;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\News;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $showCreateForm = false;
    
    protected $queryString = ['search', 'category'];
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingCategory()
    {
        $this->resetPage();
    }
    
    public function deleteNews($id)
    {
        $news = News::findOrFail($id);
        
        // Check permission - only author or admin can delete
        if (auth()->id() !== $news->author_id && !auth()->user()->isAdmin()) {
            session()->flash('error', 'You cannot delete this news.');
            return;
        }
        
        $news->delete();
        session()->flash('message', 'News deleted successfully!');
    }
    
    public function togglePin($id)
    {
        $news = News::findOrFail($id);
        
        if (auth()->user()->isAdmin()) {
            $news->update(['is_pinned' => !$news->is_pinned]);
            session()->flash('message', $news->is_pinned ? 'News pinned!' : 'News unpinned!');
        }
    }
    
    public function render()
    {
        $news = News::with('author')
            ->where('published_at', '<=', now())
            ->when($this->search, function($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->when($this->category, fn($q) => $q->where('category', $this->category))
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(10);
            
        return view('livewire.news.index', [
            'news' => $news
        ])->layout('layouts.app');
    }
}