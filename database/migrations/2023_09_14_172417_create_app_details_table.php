<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_details', function (Blueprint $table) {
            $table->id();     // primarykey | incrementing
            $table->integer('theId')->nullable();
            $table->string('appId')->nullable();
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->json('genres')->nullable();
            $table->json('genreIds')->nullable();
            $table->string('primaryGenre')->nullable();
            $table->integer('primaryGenreId')->nullable();
            $table->string('contentRating')->nullable();
            $table->string('languages')->nullable();
            $table->string('size')->nullable();
            $table->string('requiredOsVersion')->nullable();
            $table->dateTime('released')->nullable();
            $table->dateTime('updated')->nullable();
            $table->text('releaseNotes')->nullable();
            $table->string('version')->nullable();
            $table->integer('price')->nullable();
            $table->string('currency')->nullable();
            $table->boolean('free')->nullable();
            $table->integer('developerId')->nullable();
            $table->string('developer')->nullable();
            $table->string('developerUrl')->nullable();
            $table->string('developerWebsite')->nullable();
            $table->float('score')->nullable();
            $table->integer('reviews')->nullable();
            $table->float('currentVersionScore')->nullable();
            $table->integer('currentVersionReviews')->nullable();
            $table->json('screenshots')->nullable();
            $table->json('ipadScreenshots')->nullable();
            $table->json('appletvScreenshots')->nullable();
            $table->json('supportedDevices')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_details');
    }
};
