<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_active' => 'boolean',
        'accessible_roles' => 'array',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
