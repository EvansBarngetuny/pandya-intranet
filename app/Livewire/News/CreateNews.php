<?php

namespace App\Livewire\News;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\News;
use Illuminate\Support\Str;

class CreateNews extends Component
{
    use WithFileUploads;

    public $title;
    public $content;
    public $summary;
    public $category = 'general';
    public $featured_image;
    public $is_pinned = false;
    public $tags = [];
    public $tagInput = '';
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string|min:20',
        'summary' => 'nullable|string|max:500',
        'category' => 'required|in:announcement,achievement,facility,general',
        'featured_image' => 'nullable|image|max:2048', // 2MB max
        'tags' => 'nullable|array',
    ];
    
    protected $messages = [
        'title.required' => 'Please enter a news title',
        'content.required' => 'Please enter the news content',
        'content.min' => 'Content must be at least 20 characters',
        'featured_image.image' => 'File must be an image',
        'featured_image.max' => 'Image size cannot exceed 2MB',
    ];
    
    public function addTag()
    {
        if ($this->tagInput && !in_array($this->tagInput, $this->tags)) {
            $this->tags[] = $this->tagInput;
            $this->tagInput = '';
        }
    }
    
    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }
    
    public function save()
    {
        $this->validate();
        
        // Save featured image if uploaded
        $imagePath = null;
        if ($this->featured_image) {
            $imagePath = $this->featured_image->store('news-images', 'public');
        }
        
        // Create slug
        $slug = Str::slug($this->title) . '-' . uniqid();
        
        // Create news
        News::create([
            'title' => $this->title,
            'slug' => $slug,
            'content' => $this->content,
            'summary' => $this->summary ?: Str::limit($this->content, 150),
            'category' => $this->category,
            'featured_image' => $imagePath,
            'author_id' => auth()->id(),
            'tags' => $this->tags,
            'is_pinned' => $this->is_pinned,
            'published_at' => now(),
        ]);
        
        session()->flash('message', 'News published successfully!');
        return redirect()->route('news.index');
    }
    
    public function render()
    {
        return view('livewire.news.create-news')->layout('layouts.app');
    }
}