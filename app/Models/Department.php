<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'head_of_department',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function memos()
    {
        return $this->hasMany(Memo::class);
    }
}
