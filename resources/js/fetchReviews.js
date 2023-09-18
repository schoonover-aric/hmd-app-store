// resources/js/fetchReviews.js

const store = require('app-store-scraper');

async function fetchReviews(id) {
    try {
        const reviews = await store.reviews({
            id: id,
        });
        return JSON.stringify({ reviews });
    } catch (error) {
        throw error;
    }
}

module.exports = fetchReviews;
