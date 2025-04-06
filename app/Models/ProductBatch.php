<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $table = 'product_batches';

    protected $fillable = [
        'product_id',
        'barcode',
        'quantity',
        'expiry_date',
        'received_date',
        'location_id',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
