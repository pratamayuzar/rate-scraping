<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExchangeRateController;


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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [ExchangeRateController::class, 'index']);
Route::get('/rate-scrape', [ExchangeRateController::class, 'scrape'])->name('rates.scrape');
Route::get('/rate-clear', [ExchangeRateController::class, 'clear'])->name('rates.clear');
