<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
class PlacementErrorReport extends Model
{
    protected $table = 'placement_error_report';
    protected $fillable = ['product_id','wrong_location', 'correct_location', 'status', ' updated_at','created_at'];
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}