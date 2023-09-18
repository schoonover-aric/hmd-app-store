<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use App\Models\AppDetails;

class AppStoreController extends Controller
{
    public function index(Request $request)
    {
        $appsReleased = [];
        $appsUpdated = [];

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
                    if (!isset($appDetails['appDetails'])) {
                        return ['error' => 'Error fetching ratings and reviews'];
                    }

                    // Save the new appDetails to the database
                    foreach ($appDetails['appDetails'] as $app) {
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
                            'developerWebsite' => $app['developerWebsite'],
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
                    }
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
            } catch (\Throwable $e) {
                return ['error' => 'Error fetching app store data'];
            }
        } // create arrays and pass to view
        return view('home', compact('appsReleased', 'appsUpdated'));
    }

    public function fetchDataFromAppStore() // initial data fetch for Home page
    {
        try {
            // js to fetch app store data
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

    public function appDetails($id)
    {
        // Retrieve the app details from the session  // shouldn't need after save function fully implemented
        $appDetails = session('appDetails');

        // Fetch the details for selected / clicked app
        // $appDetailsById = $this->fetchAppDetailsById($id, $appDetails);
        $appDetailsById = [];

        foreach ($appDetails as $apps) {
            if ($apps['id'] == $id) {
                $appDetailsById = $apps;
            }
        }
        // Check if app details were fetched
        if ($appDetailsById) {
            // fetch ratings and reviews for same app
            $ratingsAndReviews = $this->fetchRatingsAndReviews($id);

            return view('app-details', compact('appDetailsById', 'id', 'ratingsAndReviews'));
        } else {
            return ['error' => 'Error fetching app details'];
        }
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

            // Execute the JavaScript code w/ Node.js
            $ratingsAndReviews = shell_exec('node -e ' . escapeshellarg($jsCode));

            // Parse the JSON data
            $ratingsAndReviews = json_decode($ratingsAndReviews, true);

            // Check that data was fetched
            if ($ratingsAndReviews) {
                return $ratingsAndReviews;
            } else {
                return ['error' => 'Error fetching ratings and reviews'];
            }
        } catch (\Throwable $e) {
            return ['error' => 'Error fetching ratings and reviews'];
        }
    }

    public function test()
    {
        try {
            $appDetails = $this->fetchDataFromAppStore();
            return view('test', compact('appDetails'));
        } catch (\Throwable $e) {
            return ['error' => 'Error fetching app store data'];
        }
    }
}

?>


<div class="container mx-auto flex flex-col">
    <div class="upper">
        <h1 class="text-3xl font-bold my-4">App Store Scraper</h1>
        <p class="mb-4 w-1/2">
            Welcome to the App Store Scraper! This web application allows you to explore and analyze apps from the Apple/iTunes app store. Enter your search criteria below to get started.
        </p>
        <p class="mb-4">
            Instructions:
        </p>
        <ul class="list-disc ml-6">
            <li>Enter a minimum release date to filter apps released on or after that date.</li>
            <li>Enter a minimum updated date to filter apps updated on or after that date.</li>
            <li>Click the "Search" button to view the results.</li>
            <li>Click an app Title for more details and a "Request Feedback" option from ChatGPT!</li>
        </ul>
        <p class="mt-4 mb-4">
            Get started now and explore the world of mobile apps!
        </p>
        <p class="mt-4 mb-4">
            Choose a Minimum Released date & a Minimum Updated date
            <br />
            Click on the App's Title to view more details, along with Reviews & Ratings:
        </p>
    </div>
</div>


<?php


// private function fetchAppDetailsById($id, $appDetails)
// {
//     // find app with matching 'id'
//     foreach ($appDetails as $app) {
//         if ($app['id'] == $id) {
//             return $app;
//         }
//     }
//     return null;
// }



// app-details.blade.php
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Details</title>
    <!-- Stylesheet -->
    @vite('resources/css/app.css')
</head>

<body class="bg-slate-900 text-white">
    <div class="container mx-auto mb-8">
        <h1 class="text-3xl font-semibold mt-8 mb-4" id="page-top">App Details, <a href="#ratings-reviews" class="hover:text-blue-700 transition-colors duration-300">Ratings & Reviews (below)</a>
        </h1>

        {{-- Check if the appDetailsById variable exists --}}
        {{-- @if (isset($appDetailsById)) --}}
        @if (isset($appDetails))
        <?php // dd($appDetails);
        ?>
        {{-- Create a table to display app details --}}
        <table class="table-auto w-full">
            <tbody>
                {{-- Loop through the selected app's details --}}
                {{-- @foreach ($appDetailsById as $key => $value) --}}
                {{-- @foreach ($appDetails as $key => $value) --}}
                @foreach ($appDetails->getAttributes() as $key => $value)
                <tr>
                    <td class="border-t border-r border-b border-l border-sky-500 px-2">{{ $key }}</td>
                    <td class="border-t border-r border-b border-l border-sky-500 px-2">
                        @if (is_array($value))
                        {{-- Handle arrays --}}
                        @foreach ($value as $arrayItem)
                        {{ htmlspecialchars((string) $arrayItem) }}<br>
                        @endforeach
                        @else
                        {{-- Handle strings --}}
                        {{ htmlspecialchars((string) $value) }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="mt-4">No app details available.</p>
        @endif
        <div id="ratings-reviews">
            <!-- Ratings and Reviews Section -->
            <div class="container mx-auto mb-8">
                <div class="flex" id="feedback-row">
                    <div>
                        <h2 class="text-3xl font-semibold mt-8 mb-4">Ratings</h2>
                        {{-- <h2>{{ $appDetailsById['title'] }}</h2> --}}
                        <h4>{{ $appDetails['title'] }}</h4>
                        <!-- Display other app details here -->
                        @if (!empty($ratingsAndReviews))
                        <h2>Total Ratings: {{ number_format($ratingsAndReviews['ratings']['ratings']) }}</h2>
                        @if (!empty($ratingsAndReviews['ratings']['histogram']))
                        <table>
                            <thead>
                                <tr>
                                    <th class="text-left text-lg">Rating</th>
                                    <th class="text-left text-lg">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                // Reverse the array before looping
                                $histogram = array_reverse($ratingsAndReviews['ratings']['histogram']);
                                @endphp
                                {{-- @foreach ($ratingsAndReviews['ratings']['histogram'] as $rating => $count) --}}
                                @foreach ($histogram as $rating => $count)
                                <tr>
                                    <td class="border-t border-r border-b border-l border-sky-500 p-2">
                                        <strong>{{ 5 - $rating . ' Star' . (5 - $rating === 1 ? '' : 's') }}</strong>
                                    </td>
                                    <td class="border-t border-r border-b border-l border-sky-500 p-2">
                                        {{ number_format($count) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p>No histogram data available</p>
                        @endif
                    </div>
                    <div class="ml-12">
                        <h2 class="text-3xl font-semibold mt-8 mb-4">
                            <button id="getFeedbackButton" type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded">
                                Get Feedback
                            </button>
                            <a href="#page-top">
                                <button type="button" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded">
                                    Back to top
                                </button>
                            </a>
                        </h2>
                        <div id="chatgpt-response">
                            <h2 class="text-2xl text-blue-600 font-semibold mb-4">ChatGPT Response:</h2>
                            <div id="response-body" class="border-t border-r border-b border-l border-sky-500 px-2">
                                Response body<br /><br />
                                <?php // echo "'id' of app clicked on Homepage to view these details: " . $id;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    @if (!empty($ratingsAndReviews['reviews']))
                    <h2 class="text-3xl font-semibold mt-8 mb-4">Reviews</h2>
                    <table>
                        <thead>
                            <tr>
                                <th class="text-left text-xl">User</th>
                                <th class="text-left text-xl">Rating</th>
                                <th class="text-center text-xl">Title</th>
                                <th class="text-left text-xl">Comment</th>
                                <th class="text-left text-xl">Full Review</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ratingsAndReviews['reviews'] as $review)
                            <tr>
                                <td class="border-t border-r border-b border-l border-sky-500 px-2">
                                    {{ $review['userName'] }}
                                </td>
                                <td class="border-t border-r border-b border-l border-sky-500 px-2">
                                    {{ $review['score'] }} Star{{ $review['score'] === 1 ? '' : 's' }}
                                </td>
                                <td class="border-t border-r border-b border-l border-sky-500 px-2">
                                    {{ $review['title'] }}
                                </td>
                                <td class="border-t border-r border-b border-l border-sky-500 px-2">
                                    {{ $review['text'] }}
                                </td>
                                <td class="border-t border-r border-b border-l border-sky-500 px-2 whitespace-nowrap">
                                    <a href="{{ $review['url'] }}" class="text-blue-600 hover:text-blue-500">See full review</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p>No reviews available</p>
                    @endif
                </div>
                @else
                <p>No ratings and reviews data available</p>
                @endif
                <!-- Other content of the app-details page -->
            </div>
        </div>
    </div>
    {{-- <script src="{{ asset('js/chatgpt.js') }}"></script> --}}
</body>

</html>