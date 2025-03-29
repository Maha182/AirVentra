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

    public function placementError(): BelongsTo
{
    return $this->belongsTo(PlacementErrorReport::class, 'error_id', 'product_id');
}

public function inventoryLevel(): BelongsTo
{
    return $this->belongsTo(InventoryLevelReport::class, 'error_id', 'product_id');
}

}

