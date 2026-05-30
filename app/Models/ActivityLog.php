<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ActivityLog extends Model
{
    //
    use HasFactory;

     protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];
     protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //scope for filtering by module
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
