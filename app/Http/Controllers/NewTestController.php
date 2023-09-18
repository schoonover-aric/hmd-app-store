<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewTest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewTestController extends Controller
{
    public function reviews()
    {
        $id = 1641486558;

        // Check if the "new_tests" table exists, and create it if it doesn't
        if (!Schema::hasTable('new_tests')) {
            Schema::create('new_tests', function (Blueprint $table) {
                $table->id();
                $table->string('userName');
                $table->integer('score');
                $table->string('title');
                $table->string('text');
                $table->string('url');
                $table->timestamps();
            });
        }
        // Check the data age / last time it was updated
        $latestReview = NewTest::orderBy('updated_at', 'desc')->first();
        // get timestamp value
        if ($latestReview) {
            $latestTimestamp = $latestReview->updated_at;
        } else {
            $latestTimestamp = null;
        }
        // current timestamp
        $currentTime = now();
        // calculate if data age is less than 1 hour (60 minutes)
        if ($latestTimestamp && $currentTime->diffInMinutes($latestTimestamp) < 60) {
            // if age is less than an hour -> retrieve from database
            $reviews = NewTest::all();
            // dd($reviews);
        } else {
            // if age is more than an hour -> fetch data from app-store
            $reviews = $this->fetchReviews($id);

            // Check if reviews were retrieved
            if (!isset($reviews['reviews'])) {
                return ['error' => 'Error fetching ratings and reviews'];
            }
            // dd($reviews);
            // Save the new reviews to the database
            foreach ($reviews['reviews'] as $reviewData) {
                NewTest::create([
                    'userName' => $reviewData['userName'],
                    'score' => $reviewData['score'],
                    'title' => $reviewData['title'],
                    'text' => $reviewData['text'],
                    'url' => $reviewData['url'],
                    'timestamp' => $currentTime,
                ]);
            }
        }
        // Pass the data to the view
        return view('new-test', compact('reviews'));
    }

    public function fetchReviews($id)
    {
        try {
            // JavaScript code to fetch app ratings and reviews
            $jsCode = <<<JS_CODE
                const store = require('app-store-scraper');

                async function fetchReviews() {
                    try {
                        const reviews = await store.reviews({
                            id: $id,
                        });
                        return JSON.stringify({ reviews });
                    } catch (error) {
                        throw error;
                    }
                }
                fetchReviews().then(data => {
                    console.log(data); // Log the data to check if it's being fetched
                });
            JS_CODE;

            // Execute the JavaScript code using Node.js and capture its output
            $reviews = shell_exec('node -e ' . escapeshellarg($jsCode));
            // Parse the JSON data obtained from the scraper
            $reviews = json_decode($reviews, true);
            // Check if the data was successfully fetched
            if ($reviews) {
                return $reviews;
            } else {
                return ['error' => 'Error fetching ratings and reviews'];
            }
        } catch (\Throwable $e) {
            return ['error' => 'Error fetching app store data'];
        }
    }
}



// $jsCode = <<<JS_CODE
//     const store = require('app-store-scraper');

//     async function fetchReviews() {
//         try {
//             const reviews = await store.reviews({
//                 id: $id,
//             });
//             return JSON.stringify({ reviews });
//         } catch (error) {
//             throw error;
//         }
//     }
//     fetchReviews();
// JS_CODE;

/**
 * Store a newly created resource in storage.
 */
// public function store(Request $request)
// {
//     // $reviews = new NewTest([
//     //     'userName' => $request('userName'),
//     //     'score' => $request['score'],
//     //     'title' => $request->title,
//     //     'text' => $request->text,
//     //     'url' => $request->url
//     // ]);

//     // if (!$reviews->exists) {
//     //     $reviews->save();
//     // }
// }

/**
 * Display a listing of the resource.
 */
// public function index()
// {
//     //
// }

/**
 * Show the form for creating a new resource.
 */
// public function create()
// {
//     //
// }

/**
 * Display the specified resource.
 */
// public function show(NewTest $newTest)
// {
//     //
// }

/**
 * Show the form for editing the specified resource.
 */
// public function edit(NewTest $newTest)
// {
//     //
// }

/**
 * Update the specified resource in storage.
 */
// public function update(Request $request, NewTest $newTest)
// {
//     //
// }

/**
 * Remove the specified resource from storage.
 */
    // public function destroy(NewTest $newTest)
    // {
    //     //
    // }