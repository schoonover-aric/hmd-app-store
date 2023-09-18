<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'userName',
        'score',
        'title',
        'text',
        'url'
    ];
}
