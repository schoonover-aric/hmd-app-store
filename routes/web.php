<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppStoreController;

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
