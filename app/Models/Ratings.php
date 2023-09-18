<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratings extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_ratings',
        'histogram',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'histogram' => 'json',
    ];
}
