<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations'; // Match your database table name

    protected $fillable = ['locationID', 'zone_name', 'aisle', 'rack', 'capacity', 'current_capacity'];
}
