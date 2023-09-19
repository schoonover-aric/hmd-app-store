<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Details</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Stylesheet -->
    @vite('resources/css/app.css')
    @vite('resources/js/sentiment-analysis.js')
</head>

<body class="bg-slate-900 text-white">
    <div class="container mx-auto mb-8">
        <h1 class="text-3xl font-semibold mt-8 mb-4" id="page-top">App Details | <a href="#ratings-reviews"
                class="hover:text-blue-600 transition-colors duration-300">Ratings, Reviews & Feedback <span
                    class="text-blue-500 hover:text-blue-600">
                    &darr;
                </span></a>
        </h1>
        {{-- Check if appDetails exists --}}
        @if (isset($appDetails))
            {{-- Create a table to display app details --}}
            <table class="table-auto w-full">
                <tbody>
                    {{-- Loop through the selected app's details --}}
                    @foreach ($appDetails->getAttributes() as $key => $value)
                        {{-- Skip first item (primary key) and timestamps --}}
                        @if (!$loop->first && $key !== 'created_at' && $key !== 'updated_at')
                            <tr>
                                <td class="border-t border-r border-b border-l border-sky-500 p-2">{{ $key }}
                                </td>
                                <td class="border-t border-r border-b border-l border-sky-500 p-2">
                                    @if (
                                        $key === 'languages' ||
                                            $key === 'genres' ||
                                            $key === 'genreIds' ||
                                            $key === 'screenshots' ||
                                            $key === 'ipadScreenshots' ||
                                            $key === 'appletvScreenshots' ||
                                            $key === 'supportedDevices')
                                        {{-- Handle the "languages" key --}}
                                        {{ implode(', ', json_decode($value)) }}
                                    @elseif ($key === 'released' || $key === 'updated')
                                        {{-- Handle Dates --}}
                                        {{ date('F j, Y', strtotime($value)) }}
                                    @elseif ($key === 'size' || $key === 'reviews' || $key === 'currentVersionReviews')
                                        {{ number_format($value) }}
                                    @else
                                        {{-- Handle strings --}}
                                        {{ $value }}
                                    @endif
                                </td>
                                {{-- <td class="border-t border-r border-b border-l border-sky-500 p-2"> --}}
                                {{-- @if (is_array($value)) --}}
                                {{-- Handle arrays --}}
                                {{-- {{ implode(', ', $value) }} --}}
                                {{-- @else --}}
                                {{-- Handle strings --}}
                                {{-- {{ $value }} --}}
                                {{-- @endif --}}
                                {{-- </td> --}}
                            </tr>
                        @endif
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
                        <h4 class="text-2xl text-blue-600 font-semibold mb-4">{{ $appDetails['title'] }}</h4>
                        <!-- Display other app details here -->
                        @if (!empty($ratingsAndReviews))
                            <h2 class="text-xl font-semibold mb-4 whitespace-nowrap">Total Ratings:
                                {{ number_format($ratingsAndReviews['ratings']['ratings']) }}</h2>
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
                                            // Reverse the array before looping (to display 5 star rating at top of list)
                                            $histogram = array_reverse($ratingsAndReviews['ratings']['histogram']);
                                        @endphp
                                        @foreach ($histogram as $rating => $count)
                                            <tr>
                                                <td
                                                    class="border-t border-r border-b border-l border-sky-500 p-3 whitespace-nowrap">
                                                    <strong>{{ 5 - $rating . ' Star' . (5 - $rating === 1 ? '' : 's') }}</strong>
                                                </td>
                                                <td class="border-t border-r border-b border-l border-sky-500 p-3">
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
                    <div class="ml-12 w-1/2">
                        <div>
                            <h2 class="text-3xl font-semibold mt-8 mb-4">Sentiment Response:</h2>
                            <p class="mb-2">Click "Get Feedback" to send the 10 most recent reviews to Sentiment
                                for analysis.</p>
                            <p class="mb-4">Sentiment will assign scores to words based on their overall positivity
                                or negativity and will provide a total Sentiment Score.</p>
                            <p class="text-center mb-2.5">Neutral sentiment = 0 | Positive sentiment > 0 | Negative
                                sentiment < 0</p>
                                    <div class="border-t border-r border-b border-l border-sky-500 p-2">
                                        <p class="whitespace-wrap p-2" id="sentimentResponse">Sentiment not available.
                                            Please click 'Get Feedback'
                                        </p>
                                    </div>
                        </div>
                        <div class="text-2xl font-semibold mt-8 mb-4 flex justify-around">
                            <button id="getFeedbackButton" type="button"
                                class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded">
                                Get Feedback
                            </button>
                            <a href="#page-top">
                                <button type="button"
                                    class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded">
                                    Back to Top
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="ml-12">
                        <h2 class="text-3xl text-center font-semibold mt-8 mb-8">Sentiment Distribution<br />Chart</h2>
                        <div style="width: 350px; height: 350px;">
                            <canvas id="sentimentChart"></canvas>
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
                                    <th class="text-left text-xl p-2">Version</th>
                                    <th class="text-left text-xl p-2">Rating</th>
                                    <th class="text-center text-xl">Title</th>
                                    <th class="text-left text-xl">Comment</th>
                                    <th class="text-left text-xl">Full Review</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ratingsAndReviews['reviews'] as $review)
                                    <tr>
                                        <td class="border-t border-r border-b border-l border-sky-500 p-2">
                                            {{ $review['userName'] }} <a
                                                class="text-blue-600 hover:text-blue-500 whitespace-nowrap"
                                                href="{{ $review['userUrl'] }}"><br />My
                                                Reviews</a>
                                        </td>
                                        <td class="border-t border-r border-b border-l border-sky-500 text-center p-2">
                                            {{ $review['version'] }}
                                        </td>
                                        <td class="border-t border-r border-b border-l border-sky-500 text-center p-2">
                                            {{ $review['score'] }} Star{{ $review['score'] === 1 ? '' : 's' }}
                                        </td>
                                        <td class="border-t border-r border-b border-l border-sky-500 p-2">
                                            {{ $review['title'] }}
                                        </td>
                                        <td class="border-t border-r border-b border-l border-sky-500 p-2">
                                            {{ $review['text'] }}
                                        </td>
                                        <td
                                            class="border-t border-r border-b border-l border-sky-500 p-2 whitespace-nowrap">
                                            <a href="{{ $review['url'] }}"
                                                class="text-blue-600 hover:text-blue-500">See full review</a>
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
            </div>
        </div>
    </div>
    <script>
        // Convert PHP variable $reviewsForSentiment to JavaScript variable
        const reviewsForSentiment = @json($reviewsForSentiment);
    </script>
</body>

</html>
