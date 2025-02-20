<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationCapacityCheck extends Model
{
    use HasFactory;
    protected $table = 'inventory_levels_report'; // Specify the table name
    protected $fillable = [
        'location_id',
        'scan_date',
        'detected_capacity',
        'status'
    ];

    // Relationship with Location model
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}