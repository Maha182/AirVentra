<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationCheck extends Model
{
    use HasFactory;
    protected $table = 'placement_error_report'; // Specify the table name
    protected $fillable = ['product_id', 'scan_date', 'wrong_location', 'correct_location', 'status']; // Define fillable fields
}
