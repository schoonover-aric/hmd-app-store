<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use App\Models\AppDetails;
use App\Models\Ratings;
use App\Models\Reviews;

class AppStoreController extends Controller
{
    public function index(Request $request)
    {
        $appsReleased = [];
        $appsUpdated = [];
        $appDetails = [];

        if ($request->isMethod('post')) {
            // Handle form submission and fetch data
            $minReleaseDate = $request->input('minReleaseDate');
            $minUpdatedDate = $request->input('minUpdatedDate');

            try {
                // Check if the "appDetails" table exists | create it
                if (!Schema::hasTable('app_details')) {
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
                        $table->json('languages')->nullable();
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

                // Check the data age / last time it was updated
                $latestDetails = AppDetails::orderBy('updated_at', 'desc')->first();

                // get timestamp value
                if ($latestDetails) {
                    $latestTimestamp = $latestDetails->updated_at;
                } else {
                    $latestTimestamp = null;
                }

                // current timestamp
                $currentTime = now();

                // calculate if data age is less than 1 hour (60 minutes)
                if ($latestTimestamp && $currentTime->diffInMinutes($latestTimestamp) < 60) {
                    // if age is less than an hour -> retrieve from database
                    $appDetails = AppDetails::all();
                } else {
                    // if age is more than an hour -> fetch data from app-store
                    $appDetails = $this->fetchDataFromAppStore();

                    // Check if appDetails were retrieved
                    if (!$appDetails) {
                        return ['error' => 'Error fetching app details'];
                    }

                    // Save the new appDetails to the database
                    foreach ($appDetails as $app) {
                        try {
                            AppDetails::create([
                                'theId' => $app['id'],
                                'appId' => $app['appId'],
                                'title' => $app['title'],
                                'url' => $app['url'],
                                'description' => $app['description'],
                                'icon' => $app['icon'],
                                'genres' => $app['genres'],
                                'genreIds' => $app['genreIds'],
                                'primaryGenre' => $app['primaryGenre'],
                                'primaryGenreId' => $app['primaryGenreId'],
                                'contentRating' => $app['contentRating'],
                                'languages' => $app['languages'],
                                'size' => $app['size'],
                                'requiredOsVersion' => $app['requiredOsVersion'],
                                'released' => $app['released'],
                                'updated' => $app['updated'],
                                'releaseNotes' => $app['releaseNotes'],
                                'version' => $app['version'],
                                'price' => $app['price'],
                                'currency' => $app['currency'],
                                'free' => $app['free'],
                                'developerId' => $app['developerId'],
                                'developer' => $app['developer'],
                                'developerUrl' => $app['developerUrl'],
                                'developerWebsite' => isset($app['developerWebsite']) ? $app['developerWebsite'] : null,
                                'score' => $app['score'],
                                'reviews' => $app['reviews'],
                                'currentVersionScore' => $app['currentVersionScore'],
                                'currentVersionReviews' => $app['currentVersionReviews'],
                                'screenshots' => $app['screenshots'],
                                'ipadScreenshots' => $app['ipadScreenshots'],
                                'appletvScreenshots' => $app['appletvScreenshots'],
                                'supportedDevices' => $app['supportedDevices'],
                                'timestamp' => $currentTime,
                            ]);
                        } catch (\Exception $e) {
                            error_log('Database error during save: ' . $e->getMessage());
                        }
                    }
                }
            } catch (\Throwable $e) {
                return ['error' => 'Error fetching app store data'];
            }

            // populate $appsReleased[] and $appsUpdated[]
            foreach ($appDetails as $app) {
                // Check if release date is greater than or equal to $minReleaseDate
                if (isset($app['released']) && strtotime($app['released']) >= strtotime($minReleaseDate)) {
                    $appsReleased[] = $app;
                }

                // Check if the app's updated date is greater than or equal to $minUpdatedDate
                if (isset($app['updated']) && strtotime($app['updated']) >= strtotime($minUpdatedDate)) {
                    $appsUpdated[] = $app;
                }
            }
        }
        return view('home', compact('appDetails', 'appsReleased', 'appsUpdated'));
    }
    // home / initial data fetch
    public function fetchDataFromAppStore()
    {
        try {
            // JavaScript code to fetch app store data
            $jsCode = <<<JS_CODE
                const store = require('app-store-scraper');

                async function fetchAppStoreData() {
                    try {
                        const appDetails = await store.list({
                            markets: store.markets.US,
                            // num: 20, // deafult = 50
                            fullDetail: true,
                        });
                        return JSON.stringify(appDetails);
                    } catch (error) {
                        throw error;
                    }
                }

                fetchAppStoreData().then(data => {
                    console.log(data); // Log the data to check if it's being fetched
                });
            JS_CODE;

            // Execute the JavaScript code using Node.js and capture its output
            $appDetails = shell_exec('node -e ' . escapeshellarg($jsCode));

            // Parse the JSON data obtained from the scraper
            $appDetails = json_decode($appDetails, true);

            // Check if the data was successfully fetched
            if ($appDetails) {
                return $appDetails;
            } else {
                return ['error' => 'Error fetching app store data'];
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error fetching app store data'], 500);
        }
    }

    public function appDetailsById($id)
    {
        $appDetails = AppDetails::where('theId', $id)->first();

        if (!$appDetails) {
            return ['error' => 'App not found'];
        }

        // fetch ratingsAndReviews
        $ratingsAndReviews = $this->fetchRatingsAndReviews($id);

        if (!$ratingsAndReviews) {
            return ['error' => 'Ratings & Reviews not found'];
        }

        // create ratings and reviews from ratingsAndReviews
        $ratings = $ratingsAndReviews['ratings'];
        $reviews = $ratingsAndReviews['reviews'];

        // Check if the "ratings" table exists | create it
        if (!Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('total_ratings')->nullable();
                $table->json('histogram')->nullable();
                $table->timestamps();
            });
        }

        // Save the ratings
        if (isset($ratings['ratings']) && isset($ratings['histogram'])) {
            try {
                Ratings::create([
                    'total_ratings' => $ratings['ratings'],
                    'histogram' => $ratings['histogram'],
                ]);
            } catch (\Exception $e) {
                error_log('Database error during create: ' . $e->getMessage());
            }
        }

        // Check if the "reviews" table exists | create it
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
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

        // save the reviews
        foreach ($reviews as $review) {
            try {
                Reviews::create([
                    'reviewId' => $review['id'],
                    'userName' => $review['userName'],
                    'userUrl' => $review['userUrl'],
                    'version' => $review['version'],
                    'score' => $review['score'],
                    'title' => $review['title'],
                    'text' => $review['text'],
                    'url' => $review['url'],
                ]);
            } catch (\Exception $e) {
                error_log('Database error during save: ' . $e->getMessage());
            }
        }

        // Fetch the last 10 reviews from the database for Sentiment analysis
        $last10Reviews = Reviews::orderBy('created_at', 'desc')->limit(10)->get();

        // Combine the text (review portion) of the 10 reviews
        $reviewsForSentiment = [];
        foreach ($last10Reviews as $review) {
            $reviewsForSentiment[] = $review->text;
        }

        // Sentiment API / Response
        $command = 'node ' . base_path('resources/js/sentiment-analysis.js');
        $output = shell_exec($command);

        return view('app-details', compact('appDetails', 'ratingsAndReviews', 'output', 'reviewsForSentiment'));
    }

    public function fetchRatingsAndReviews($id)
    {
        try {
            // JavaScript code to fetch app ratings and reviews
            $jsCode = <<<JS_CODE
                const store = require('app-store-scraper');

                async function fetchRatingsAndReviews() {
                    try {
                        const ratings = await store.ratings({
                            id: $id,
                        });
                        const reviews = await store.reviews({
                            id: $id,
                        });
                        return JSON.stringify({ ratings, reviews });
                    } catch (error) {
                        throw error;
                    }
                }

                fetchRatingsAndReviews().then(data => {
                    console.log(data); // Log the data to check if it's being fetched
                });
            JS_CODE;

            // Execute the JavaScript code using Node.js and capture its output
            $ratingsAndReviews = shell_exec('node -e ' . escapeshellarg($jsCode));

            // Parse the JSON data obtained from the scraper
            $ratingsAndReviews = json_decode($ratingsAndReviews, true);

            // Check if the data was successfully fetched
            if ($ratingsAndReviews) {
                return $ratingsAndReviews;
            } else {
                return ['error' => 'Error fetching ratings and reviews'];
            }
        } catch (\Throwable $e) {
            return ['error' => 'Error fetching ratings and reviews'];
        }
    }
}
