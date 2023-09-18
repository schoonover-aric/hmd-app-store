<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppStoreController;
use App\Http\Controllers\NewTestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home Page
Route::get('/', [AppStoreController::class, 'index'])->name('home');
Route::post('/', [AppStoreController::class, 'index'])->name('home');
Route::get('/home', [AppStoreController::class, 'index'])->name('home');
Route::post('/home', [AppStoreController::class, 'index'])->name('home');

// App Details Page        // not sure if I can pass more than 1 item...
Route::get('/app-details/{id}', [AppStoreController::class, 'appDetailsById'])->name('app-details');
Route::post('/app-details/{id}', [AppStoreController::class, 'appDetailsById'])->name('app-details');

// NewTest
Route::get('/new-test', [NewTestController::class, 'reviews']);
Route::post('/new-test', [NewTestController::class, 'reviews']);

// Sentiment Analysis
Route::get('/analyze-sentiment', [AppStoreController::class, 'analyzeSentiment'])->name('analyze-sentiment');

Route::get('/sentiment-analysis', [AppStoreController::class, 'analyzeSentiment']);

// Route::get('/analyze-sentiment', 'AppStoreController@analyzeSentiment');

// Route::get('/analyze-sentiment', 'AppStoreController@analyzeSentiment');

// Route::get('/new-test/{id}', [NewTestController::class, 'reviews'])->name('new-test');
// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/home', function () {
//     return view('home');
// });
