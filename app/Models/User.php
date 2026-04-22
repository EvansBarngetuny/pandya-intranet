<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'staff_number', 'department_id',
        'role', 'phone', 'profile_photo', 'position', 'is_active',
        'hire_date', 'permissions'
            ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

     protected $casts = [
        'is_active' => 'boolean',
        'hire_date' => 'date',
        'permissions' => 'array'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function readMemos()
{
    return $this->belongsToMany(Memo::class, 'memo_reads')
        ->withPivot('read_at')
        ->withTimestamps();
}
// Role checks
public function isAdmin() { return $this->role === 'admin'; }
public function isHOD() { return $this->role === 'hod'; }
public function isStaff() { return $this->role === 'staff'; }
// Permission checks
    public function canCreateMemos()
    {
        return in_array($this->role, ['admin', 'hod']);
    }
    public function canPublishNews()
    {
        return in_array($this->role, ['admin']);
    }
     public function canManageStaff()
    {
        return in_array($this->role, ['admin']);
    }
    public function canViewReports()
    {
        return in_array($this->role, ['admin', 'hod']);
    }
      // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
     public function createdMemos()
    {
        return $this->hasMany(Memo::class, 'created_by');
    }
       public function memoAcknowledgments()
    {
        return $this->hasMany(MemoAcknowledgment::class);
    }
       public function unreadMemos()
    {
        return Memo::where('status', 'published')
            ->whereDoesntHave('acknowledgments', function($q) {
                $q->where('user_id', $this->id);
            })
            ->where(function($q) {
                $q->where('audience_type', 'all')
                    ->orWhere(function($q2) {
                        $q2->where('audience_type', 'departments')
                            ->whereJsonContains('audience_ids', $this->department_id);
                    })
                    ->orWhere(function($q2) {
                        $q2->where('audience_type', 'specific_users')
                            ->whereJsonContains('audience_ids', $this->id);
                    });
            })->get();
    }
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }
     public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        return 'https://ui-avatars.com/api/?background=2563eb&color=fff&name=' . urlencode($this->name);
    }
}
