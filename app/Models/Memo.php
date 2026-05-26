<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Memo extends Model
{
    //
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ARCHIVED = 'archived';

        protected $table = 'memos';

    protected $fillable = [
        'memo_number', 'title', 'content', 'created_by', 'department_id',
        'priority', 'effective_date',     // Add this
    'expiry_date', 'audience_type', 'audience_ids', 'require_acknowledgment',
        'attachments', 'status', 'published_at', 'expires_at','published_by',
        'approved_at',
        'approved_by',
        'rejection_reason'
    ];
     protected $casts = [
        'effective_date' => 'datetime',  // Change from 'date' to 'datetime'
        'expiry_date' => 'datetime',
        'audience_ids' => 'array',
        'attachments' => 'array',
         'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'audience_ids' => 'array',
        'recipients' => 'array',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'require_acknowledgment' => 'boolean'
    ];
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
     public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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
        // Extract department IDs from the nested array structure
        $departmentIds = collect($this->audience_ids)->pluck('id')->toArray();
        return User::whereIn('department_id', $departmentIds)
            ->where('is_active', true)
            ->count();
    } else {
        // Extract user IDs from the nested array structure
        $userIds = collect($this->audience_ids)->pluck('id')->toArray();
        return User::whereIn('id', $userIds)
            ->where('is_active', true)
            ->count();
    }
    }
     public function getUnacknowledgedUsers()
    {
        $acknowledgedUserIds = $this->acknowledgments()->pluck('user_id')->toArray();

    if ($this->audience_type === 'all') {
        return User::whereNotIn('id', $acknowledgedUserIds)
            ->where('is_active', true)
            ->get();
    } elseif ($this->audience_type === 'departments') {
        // Extract department IDs from the nested array structure
        $departmentIds = collect($this->audience_ids)->pluck('id')->toArray();
        return User::whereIn('department_id', $departmentIds)
            ->whereNotIn('id', $acknowledgedUserIds)
            ->where('is_active', true)
            ->get();
    } else {
        // Extract user IDs from the nested array structure
        $userIds = collect($this->audience_ids)->pluck('id')->toArray();
        return User::whereIn('id', $userIds)
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
        public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPendingApproval(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
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
      public function canBePublishedBy(User $user): bool
    {
        return $user->isAdmin() && $this->isPendingApproval();
    }

    public function canBeApprovedBy(User $user): bool
    {
        return $user->isAdmin() && $this->isPendingApproval();
    }

    public function canBeEditedBy(User $user): bool
    {
        return ($user->id === $this->created_by || $user->isAdmin()) && $this->isDraft();
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $user->isAdmin();
    }
    public function submitForApproval(User $user)
    {
        if ($this->isDraft() && ($user->id === $this->created_by || $user->isHOD() || $user->isAdmin())) {
            $this->update([
                'status' => self::STATUS_PENDING_APPROVAL,
            ]);
            return true;
        }
        return false;
    }

    public function approve(User $user, $rejectionReason = null)
    {
        if (!$user->isAdmin()) {
            return false;
        }

        if ($rejectionReason) {
            // Reject
            $this->update([
                'status' => self::STATUS_REJECTED,
                'rejection_reason' => $rejectionReason,
                'approved_at' => now(),
                'approved_by' => $user->id,
            ]);
            return false;
        } else {
            // Approve and publish
            $this->update([
                'status' => self::STATUS_PUBLISHED,
                'published_at' => now(),
                'published_by' => $user->id,
                'approved_at' => now(),
                'approved_by' => $user->id,
            ]);
            return true;
        }
    }

    public function publish(User $user)
    {
        if ($user->isAdmin() && $this->isPendingApproval()) {
            $this->update([
                'status' => self::STATUS_PUBLISHED,
                'published_at' => now(),
                'published_by' => $user->id,
            ]);
            return true;
        }
        return false;
    }
  // Add method to get audit trail for a specific user
public function getUserAuditTrail(User $user)
{
    return [
        'read' => $this->readBy()->where('user_id', $user->id)->first(),
        'acknowledged' => $this->acknowledgments()->where('user_id', $user->id)->first(),
    ];
}

// Get all users with their read/acknowledge status
public function getAllUsersAuditTrail()
{
    $users = $this->getTargetAudience();
    $auditTrail = [];

    foreach ($users as $user) {
        $auditTrail[] = [
            'user' => $user,
            'read_at' => $this->readBy()->where('user_id', $user->id)->first()?->pivot->read_at,
            'acknowledged_at' => $this->acknowledgments()->where('user_id', $user->id)->first()?->acknowledged_at,
            'status' => $this->getUserStatus($user),
        ];
    }

    return $auditTrail;
}
// Get target audience for this memo
public function getTargetAudience()
{
    if ($this->audience_type === 'all') {
        return User::where('is_active', true)->get();
    } elseif ($this->audience_type === 'departments') {
        // Extract department IDs from the nested array structure
        $departmentIds = collect($this->audience_ids)->pluck('id')->toArray();
        return User::whereIn('department_id', $departmentIds)
            ->where('is_active', true)
            ->get();
    } else {
        // Extract user IDs from the nested array structure
        $userIds = collect($this->audience_ids)->pluck('id')->toArray();
        return User::whereIn('id', $userIds)
            ->where('is_active', true)
            ->get();
    }
}
// Get user's status (Not Read, Read, Acknowledged)
public function getUserStatus(User $user)
{
    $isRead = $this->isReadBy($user);
    $isAcknowledged = $this->acknowledgedBy($user);

    if ($isAcknowledged) {
        return 'acknowledged';
    } elseif ($isRead) {
        return 'read';
    } else {
        return 'unread';
    }
}
public function getFormattedAudienceAttribute()
{
    if ($this->audience_type === 'all') {
        return 'All Staff';
    }

    if ($this->audience_type === 'departments') {
        $deptNames = [];
        foreach ($this->audience_ids as $audience) {
            if ($audience['type'] === 'department') {
                $dept = Department::find($audience['id']);
                if ($dept) {
                    $deptNames[] = $dept->name;
                }
            }
        }
        return implode(', ', $deptNames);
    }

    if ($this->audience_type === 'specific_users') {
        $userNames = [];
        foreach ($this->audience_ids as $audience) {
            if ($audience['type'] === 'user') {
                $user = User::find($audience['id']);
                if ($user) {
                    $userNames[] = $user->name;
                }
            }
        }
        return implode(', ', $userNames);
    }

    return 'Unknown';
}

}
