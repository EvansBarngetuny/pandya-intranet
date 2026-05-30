<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; // Add this import
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Add this import

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'uploaded_by',
        'download_count',
        'version',
        'effective_date',
        'is_active',
        'accessible_roles',
        'require_acknowledgment',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_active' => 'boolean',
        'accessible_roles' => 'array',
        'require_acknowledgment' => 'boolean',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    // View tracking
    public function views(): HasMany
    {
        return $this->hasMany(DocumentView::class);
    }
    
    public function viewedBy(User $user): bool
    {
        return $this->views()->where('user_id', $user->id)->exists();
    }
    
    public function markAsViewed(User $user)
    {
        if (!$this->viewedBy($user)) {
            DocumentView::create([
                'document_id' => $this->id,
                'user_id' => $user->id,
                'viewed_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
    
    // Acknowledgment tracking
    public function acknowledgments(): HasMany
    {
        return $this->hasMany(DocumentAcknowledgment::class);
    }
    
    public function acknowledgedBy(User $user): bool
    {
        return $this->acknowledgments()->where('user_id', $user->id)->exists();
    }
    
    public function acknowledge(User $user)
    {
        if (!$this->acknowledgedBy($user) && $this->require_acknowledgment) {
            DocumentAcknowledgment::create([
                'document_id' => $this->id,
                'user_id' => $user->id,
                'acknowledged_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'acknowledged_document',
                'module' => 'documents',
                'description' => "Acknowledged document: {$this->title}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return true;
        }
        return false;
    }
    
    // Download tracking
    public function downloads(): HasMany
    {
        return $this->hasMany(DocumentDownload::class);
    }
    
    public function downloadedBy(User $user): bool
    {
        return $this->downloads()->where('user_id', $user->id)->exists();
    }
    
    public function incrementDownloadCount()
    {
        $this->increment('download_count');

        // Record the download in the downloads table
        DocumentDownload::create([
            'document_id' => $this->id,
            'user_id' => auth()->id(),
            'downloaded_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'downloaded_document',
            'module' => 'documents',
            'description' => "Downloaded document: {$this->title}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    // Get Audit trail for document
    public function getAuditTrail()
    {
        $users = User::where('is_active', true)->get();
        $auditTrail = [];

        foreach ($users as $user) {
            $viewed = $this->views()->where('user_id', $user->id)->first();
            $acknowledged = $this->acknowledgments()->where('user_id', $user->id)->first();
            $downloaded = $this->downloads()->where('user_id', $user->id)->exists();

            $auditTrail[] = [
                'user' => $user,
                'viewed_at' => $viewed?->viewed_at,
                'acknowledged_at' => $acknowledged?->acknowledged_at,
                'downloaded' => $downloaded,
                'status' => $this->getUserStatus($user),
            ];
        }
        
        return $auditTrail; // Make sure to return the array
    }
    
    public function getUserStatus($user)
    {
        if ($this->acknowledgedBy($user)) {
            return 'acknowledged';
        } elseif ($this->viewedBy($user)) {
            return 'viewed';
        } else {
            return 'pending';
        }
    }
    
    public function getViewPercentageAttribute()
    {
        $total = User::where('is_active', true)->count();
        if ($total === 0) return 0;
        return round(($this->views()->count() / $total) * 100, 2);
    }
    
    public function getAcknowledgmentPercentageAttribute()
    {
        $total = User::where('is_active', true)->count();
        if ($total === 0) return 0;
        return round(($this->acknowledgments()->count() / $total) * 100, 2);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}