<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    
    protected $fillable = [
        'error_type',
        'error_id',
        'assigned_to',
        'status',
        'deadline'
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function error(): BelongsTo
    {
        return $this->morphTo();
    }
}
