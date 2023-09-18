<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewId',
        'userName',
        'userUrl',
        'version',
        'score',
        'title',
        'text',
        'url',
        'created_at',
        'updated_at',
    ];
}
