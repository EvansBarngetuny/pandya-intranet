<?php

namespace App\Livewire\News;

use Livewire\Component;
use App\Models\News;

class ShowNews extends Component
{
    public News $news;
    
    public function mount(News $news)
    {
        $this->news = $news;
    }
    
    public function render()
    {
        return view('livewire.news.show-news')->layout('layouts.app');
    }
}