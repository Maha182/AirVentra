<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    public $timestamps = false; // <---- ADD THIS LINE

    protected $primaryKey = 'locationID';
    protected $casts = [
        'current_capacity' => 'integer',
        'capacity' => 'integer',
    ];
    
    public $incrementing = false; // Set to true if primary key is auto-incrementing

    protected $fillable = ['locationID', 'zone_name', 'current_capacity', 'capacity', 'aisle'];

    public function products()
    {
        return $this->hasMany(Product::class, 'location_id', 'locationID');
    }
}