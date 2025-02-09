<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    public $timestamps = false; // <---- ADD THIS LINE

    protected $primaryKey = 'id';
    protected $casts = [
        'current_capacity' => 'integer',
        'capacity' => 'integer',
    ];
    
    public $incrementing = false; // Set to true if primary key is auto-incrementing

    protected $fillable = ['id', 'zone_name', 'current_capacity', 'capacity', 'aisle'];

    public function products()
    {
        return $this->hasMany(Product::class, 'location_id', 'id');
    }
}