<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// table name will be: "app_details"
class AppDetails extends Model
{
    use HasFactory;

    protected $casts = [
        'genres' => 'json',
        'genreIds' => 'json',
        'languages' => 'json',
        'screenshots' => 'json',
        'ipadScreenshots' => 'json',
        'appletvScreenshots' => 'json',
        'supportedDevices' => 'json',
    ];

    // protected $guarded = [];

    protected $fillable = [
        'theId',
        'appId',
        'title',
        'url',
        'description',
        'icon',
        'genres',
        'genreIds',
        'primaryGenre',
        'primaryGenreId',
        'contentRating',
        'languages',
        'size',
        'requiredOsVersion',
        'released',
        'updated',
        'releaseNotes',
        'version',
        'price',
        'currency',
        'free',
        'developerId',
        'developer',
        'developerUrl',
        'developerWebsite',
        'score',
        'reviews',
        'currentVersionScore',
        'currentVersionReviews',
        'screenshots',
        'ipadScreenshots',
        'appletvScreenshots',
        'supportedDevices',
        'created_at',
        'updated_at',
    ];
}

// public $incrementing = true;

// protected $primaryKey = 'p_key';

// public $timestamps = true;