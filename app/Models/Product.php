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
    protected $fillable = ['id', 'title', 'description', 'main_category', 'quantity', 'location_id', 'barcode_path'];

    
    // If your primary key is not auto-incrementing, set this property:
    public $incrementing = false; // Because we're manually setting 'id'
    protected $keyType = 'string';
    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->id = self::generateProductId($product->main_category);
        });
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'locationID');
    }
    public static function generateProductId($category)
    {
        // Mapping categories to letters
        $categoryCodes = [
            'Books' => 'B',
            'Beauty' => 'C',
            'Grocery' => 'G',
        ];

        $prefix = $categoryCodes[$category] ?? 'X'; // Default 'X' if category is not found

        // Find the highest existing ID for the category
        $latestProduct = self::where('id', 'LIKE', "$prefix%")->orderBy('id', 'desc')->first();

        if ($latestProduct) {
            $lastNumber = (int) substr($latestProduct->id, 1); // Extract numeric part
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 51; // Start from 51 if no previous entries
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT); // Format as B0051, C0052, etc.
    }
}
