<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NewTest</title>
    <!-- Stylesheet -->
    @vite('resources/css/app.css')
</head>

<body class="bg-slate-900 text-white">
    <div class="container mx-auto mb-8">
        @if (!empty($reviews))
            <?php
            // dd($reviews);
            ?>
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
                    @foreach ($reviews as $review)
                        <tr>
                            <td class="border-t border-r border-b border-l border-sky-500 px-2">
                                {{ $review['userName'] }}</td>
                            <td class="border-t border-r border-b border-l border-sky-500 px-2">
                                {{ $review['score'] }} Star{{ $review['score'] === 1 ? '' : 's' }}</td>
                            <td class="border-t border-r border-b border-l border-sky-500 px-2">
                                {{ $review['title'] }}</td>
                            <td class="border-t border-r border-b border-l border-sky-500 px-2">
                                {{ $review['text'] }}</td>
                            <td class="border-t border-r border-b border-l border-sky-500 px-2 whitespace-nowrap">
                                <a href="{{ $review['url'] }}">See full review</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No reviews available</p>
        @endif
    </div>
</body>

</html>
