<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;


class News extends Model
{
    //
    protected $fillable = [
        'title', 'content', 'slug', 'featured_image', 'author_id',
        'category', 'tags', 'is_pinned', 'show_on_homepage', 'published_at'
    ];
    protected $casts = [
        'tags' => 'array',
        'is_pinned' => 'boolean',
        'show_on_homepage' => 'boolean',
        'published_at' => 'datetime',
    ];
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function scopePublished(Builder $query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }
      public function scopeHomepage(Builder $query)
    {
        return $query->where('show_on_homepage', true);
    }

    public function scopePinned(Builder $query)
    {
        return $query->where('is_pinned', true);
    }
      public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at <= now();
    }
      public function getCategoryIconAttribute()
    {
        return match($this->category) {
            'announcement' => '📢',
            'achievement' => '🏆',
            'facility' => '🏥',
            default => '📰',
        };
    }
}
