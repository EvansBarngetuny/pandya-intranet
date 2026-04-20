<?php

namespace App\Livewire\News;

use App\Models\News;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $showCreateForm = false;
    public $title, $content, $category_selected, $is_pinned = false;
    public $tags = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category_selected' => 'required|in:announcement,event,achievement,general',
        'is_pinned' => 'boolean',
    ];

    public function createNews()
    {
        $this->validate();

        News::create([
            'title' => $this->title,
            'slug' => Str::slug($this->title) . '-' . uniqid(),
            'content' => $this->content,
            'author_id' => auth()->id(),
            'category' => $this->category_selected,
            'tags' => $this->tags,
            'is_pinned' => $this->is_pinned,
            'published_at' => now()
        ]);

        session()->flash('message', 'News published successfully!');
        $this->reset(['showCreateForm', 'title', 'content', 'category_selected', 'tags', 'is_pinned']);
        $this->dispatch('news-created');
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
        return view('livewire.news.index', compact('news'));
    }
}
