<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Event extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'start_time',
        'end_time',
        'location',
        'audience',
        'audience_ids',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'audience_ids' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
