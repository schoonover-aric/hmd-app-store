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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();    // Primary Key | incrementing

            $table->string('reviewId')->nullable();
            $table->string('userName')->nullable();
            $table->string('userUrl')->nullable();
            $table->string('version')->nullable();
            $table->integer('score')->nullable();
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->string('url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings_and_reviews');
    }
};
