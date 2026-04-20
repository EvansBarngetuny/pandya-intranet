<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    //
    protected $fillable = [
        'title', 'content', 'slug', 'featured_image', 'author_id',
        'category', 'tags', 'is_pinned', 'published_at'
    ];
    protected $casts = [
        'tags' => 'array',
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
    ];
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
