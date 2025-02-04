<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = ['productID', 'title', 'description', 'main_category', 'location_id', 'barcode_path'];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'locationID');
    }
}
