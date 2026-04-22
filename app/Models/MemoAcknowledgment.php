<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoAcknowledgment extends Model
{
    //
    use HasFactory;

     protected $table = 'memo_acknowledgments';

    protected $fillable = [
        'memo_id',
        'user_id',
        'acknowledged_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    public function memo()
    {
        return $this->belongsTo(Memo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
