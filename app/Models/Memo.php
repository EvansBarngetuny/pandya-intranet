<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Memo extends Model
{
    //
    protected $fillable = [
        'memo_number', 'title', 'content', 'created_by', 'department_id',
        'priority', 'effective_date',     // Add this
    'expiry_date', 'audience_type', 'audience_ids', 'require_acknowledgment',
        'attachments', 'status', 'published_at', 'expires_at'
    ];
     protected $casts = [
        'effective_date' => 'datetime',  // Change from 'date' to 'datetime'
        'expiry_date' => 'datetime',
        'audience_ids' => 'array',
        'attachments' => 'array',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'require_acknowledgment' => 'boolean'
    ];
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
     public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function acknowledgments()
    {
        return $this->hasMany(MemoAcknowledgment::class);
    }
     public function acknowledgedBy(User $user)
    {
        return $this->acknowledgments()->where('user_id', $user->id)->exists();
    }
     public function getAcknowledgmentPercentageAttribute()
    {
        $total = $this->getTargetAudienceCount();
        if ($total === 0) return 0;
        return round(($this->acknowledgments()->count() / $total) * 100, 2);
    }
       public function getTargetAudienceCount()
    {
        if ($this->audience_type === 'all') {
            return User::where('is_active', true)->count();
        } elseif ($this->audience_type === 'departments') {
            return User::whereIn('department_id', $this->audience_ids)
                ->where('is_active', true)
                ->count();
        } else {
            return User::whereIn('id', $this->audience_ids)
                ->where('is_active', true)
                ->count();
        }
    }
     public function getUnacknowledgedUsers()
    {
        $acknowledgedUserIds = $this->acknowledgments()->pluck('user_id');

        if ($this->audience_type === 'all') {
            return User::whereNotIn('id', $acknowledgedUserIds)
                ->where('is_active', true)
                ->get();
        } elseif ($this->audience_type === 'departments') {
            return User::whereIn('department_id', $this->audience_ids)
                ->whereNotIn('id', $acknowledgedUserIds)
                ->where('is_active', true)
                ->get();
        } else {
            return User::whereIn('id', $this->audience_ids)
                ->whereNotIn('id', $acknowledgedUserIds)
                ->where('is_active', true)
                ->get();
        }
    }
     public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
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
