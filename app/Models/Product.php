<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    // Define the primary key for this model
    protected $primaryKey = 'id'; 

    // If your primary key is not auto-incrementing, set this property:
    public $incrementing = false;  // Set to true if primary key is auto-incrementing
    public $timestamps = false; // <---- ADD THIS LINE

    protected $fillable = ['id', 'title', 'description', 'main_category', 'location_id', 'barcode_path', 'quantity'];

    // If the productID is a string (like UUID), use casting
    protected $casts = [
        'productID' => 'string',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'locationID');
    }
}
