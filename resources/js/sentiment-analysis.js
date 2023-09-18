import Sentiment from 'sentiment';

const sentiment = new Sentiment();

document.addEventListener('DOMContentLoaded', function () {
    const analyzeButton = document.getElementById('getFeedbackButton');
    const sentimentResponse = document.getElementById('sentimentResponse');

    analyzeButton.addEventListener('click', function () {
        // Use the reviewsForSentiment variable for Sentiment analysis
        const text = reviewsForSentiment.join(' '); // Join the reviews into a single string
        console.log(text);
        const result = sentiment.analyze(text);

        // Update the sentimentResponse element with the analysis result
        sentimentResponse.innerHTML = "The Sentiment Score is: " + result.score +
            "<br /><br /> There were " + result.words.length + " Words and " + result.tokens.length + " Tokens (total words & emojis) used in this analysis." +
            "<br />" + result.positive.length + " of the words were Positive." +
            "<br />" + result.negative.length + " of the Words were Negative." +
            "<br /><br />" + "The 10 reviews used for this analysis are logged to the console as a single string.";

        // Count positive, negative, and neutral sentiments
        var positiveCount = result.positive.length;
        var negativeCount = result.negative.length;
        var totalWords = result.words.length;
        var neutralCount = totalWords - (positiveCount + negativeCount);

        // Create a Sentiment Distribution chart
        var ctx = document.getElementById('sentimentChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'pie', // Pie chart for sentiment distribution
            data: {
                labels: ['Positive', 'Negative', 'Neutral'],
                datasets: [{
                    data: [positiveCount, negativeCount, neutralCount],
                    backgroundColor: ['green', 'red', 'gray'], // Define colors for segments
                }],
            },
            options: {
                // Add chart options as needed (e.g., legend, tooltips)
            },
        });
    });
});
