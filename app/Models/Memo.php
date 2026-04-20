<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Memo extends Model
{
    //
    protected $fillable = [
        'memo_number',
        'title',
        'content',
        'created_by',
        'department_id',
        'priority',
        'effective_date',
        'expiry_date',
        'attachments',
        'recipients',
        'status',
        'published_at'
    ];
     protected $casts = [
        'attachments' => 'array',
        'recipients' => 'array',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'published_at' => 'datetime'
    ];
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
     public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function readBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memo_reads')
        ->withPivot('read_at')
        ->withTimestamps();
    }
     public function isReadBy(User $user): bool
    {
        return $this->readBy()->where('user_id', $user->id)->exists();
    }

    public function getReadPercentageAttribute(): float
    {
        $total = $this->getRecipientCount();
        if ($total === 0) return 0;
        return round(($this->readBy()->count() / $total) * 100, 2);
    }

    protected function getRecipientCount(): int
    {
        if (empty($this->recipients)) return 0;

        $count = 0;
        foreach ($this->recipients as $recipient) {
            if ($recipient['type'] === 'department') {
                $count += User::where('department_id', $recipient['id'])->count();
            } else {
                $count++;
            }
        }
        return $count;
    }

}
