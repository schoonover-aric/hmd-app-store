<!DOCTYPE html>
<html>

<head>
    <title>H.M.D. Search Apps</title>
    <!-- Stylesheet -->
    @vite('resources/css/app.css')
</head>

<body class="bg-slate-900 text-white">
    <div class="container mx-auto px-16 mb-8">
        <h1 class="text-3xl text-blue-600 font-semibold mt-8 mb-4">H.M.D. - App Store Search</h1>
        <div class="container mx-auto flex flex-col">
            <div class="upper">
                <p class="mb-4">
                    Welcome! This web application allows you to explore and analyze apps from
                    the Apple/iTunes app store.
                </p>
                <p class="mb-4">
                    Instructions:
                </p>
                <ul class="list-disc ml-6">
                    <li>Enter a minimum release date to filter apps released on or after that date.</li>
                    <li>Enter a minimum updated date to filter apps updated on or after that date.</li>
                    <li>Click the "Search" button to view the results.</li>
                    <li>Click an app Title for more details and a "Request Feedback" option for a Sentiment Analysis!
                    </li>
                </ul>
                <p class="mt-4 mb-4">
                    Let's get started!
                </p>
            </div>
        </div>
        <?php
        ?>
        {{-- App Search Form --}}
        <form method="post" action="{{ route('home') }}" class="mb-8 mt-4">
            @csrf
            <div class="flex flex-col sm:flex-row sm:space-x-4">
                <!-- form input fields for minimum release and updated dates -->
                <div class="mt-4 sm:mt-0 flex items-center text-xl">
                    <label for="minReleaseDate">Minimum Release Date: </label>
                    <input class="text-black mx-2" type="date" name="minReleaseDate" id="minReleaseDate"
                        value="{{ request('minReleaseDate') }}">
                </div>
                <div class="mt-4 sm:mt-0 flex items-center text-xl">
                    <label for="minUpdatedDate">Minimum Updated Date: </label>
                    <input class="text-black mx-2" type="date" name="minUpdatedDate" id="minUpdatedDate"
                        value="{{ request('minUpdatedDate') }}">
                </div>
                <div class="mt-4 sm:mt-0">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded">Search</button>
                </div>
            </div>
        </form>
        <div class="flex flex-row space-x-12">
            {{-- Display Apps Released --}}
            <div class="flex-1">
                @if (count($appsReleased) > 0)
                    <h2 class="text-2xl text-blue-600 font-semibold mt-8 mb-4">Apps Released:</h2>
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="text-left text-2xl">App</th>
                                <th class="text-left text-2xl">Released Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appsReleased as $app)
                                <tr>
                                    <td class="border-t border-r border-b border-l border-sky-500 p-2">
                                        @if (isset($app['theId']))
                                            <a class="hover:text-blue-500"
                                                href="{{ route('app-details', ['id' => $app['theId']]) }}">{{ $app['title'] }}</a>
                                            <a class="text-blue-600 hover:text-blue-500"
                                                href="{{ route('app-details', ['id' => $app['theId']]) }}">View
                                                Details</a>
                                        @else
                                            <!-- Handle the case where 'theId' is not set -->
                                            <span class="text-yellow-500">Minimum Release Date Not Selected</span>
                                        @endif
                                    </td>
                                    <td class="border-t border-r border-b border-l border-sky-500 p-2 text-center">
                                        @if (isset($app['released']))
                                            {{ date('F j, Y', strtotime($app['released'])) }}
                                        @else
                                            <!-- Handle the case where 'released' is not set -->
                                            <span class="text-red-500">Release date not available</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No apps released meet the criteria. Please choose a date!</p>
                @endif
            </div>
            <div class="flex-1">
                {{-- Display Apps Updated --}}
                @if (count($appsUpdated) > 0)
                    <h2 class="text-2xl text-blue-600 font-semibold mt-8 mb-4">Apps Updated:</h2>
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="text-left text-2xl">App</th>
                                <th class="text-left text-2xl">Updated Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appsUpdated as $app)
                                <tr>
                                    <td class="border-t border-r border-b border-l border-sky-500 p-2">
                                        @if (isset($app['theId']))
                                            <a class="hover:text-blue-500"
                                                href="{{ route('app-details', ['id' => $app['theId']]) }}">{{ $app['title'] }}</a>
                                            <a class="text-blue-600 hover:text-blue-500"
                                                href="{{ route('app-details', ['id' => $app['theId']]) }}">View
                                                Details</a>
                                        @else
                                            <!-- Handle the case where 'theId' is not set -->
                                            <span class="text-yellow-500">Minimum Update Date Not Selected</span>
                                        @endif
                                    </td>
                                    <td class="border-t border-r border-b border-l border-sky-500 p-2 text-center">
                                        @if (isset($app['updated']))
                                            {{ date('F j, Y', strtotime($app['updated'])) }}
                                        @else
                                            <!-- Handle the case where 'updated' is not set -->
                                            <span class="text-red-500">Update date not selected</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No apps updated meet the criteria. Please choose a date!</p>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
