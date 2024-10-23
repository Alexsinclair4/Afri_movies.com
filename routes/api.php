<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

*/

use App\Http\Controllers\MovieController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;


Route::group([
    'middleware' =>'api',
    'prefix'     =>'users'
], function($router){
    Route::post('signin',[UserController::class, 'signIn']);
    Route::post('signup',[UserController::class, 'register']);
    Route::post('logout',[UserController::class, 'logout']);
    Route::patch('approveusers/{id}',[UserController::class, 'approveUser']);
});

Route::group([
    'middleware' =>'api',
    'prefix'     =>'reviews'
], function($router){
    Route::post('moviereview',[ReviewController::class, 'submitReviews']);
    Route::get('allreview',[ReviewController::class, 'allReviews']);
    Route::patch('approverating/{id}',[ReviewController::class, 'approveReview']);
});

Route::group([
    'middleware' =>'api',
    'prefix'     =>'movies'
], function($router){
    Route::get('allmovie',[MovieController::class, 'allMovie']);
    Route::get('titlesearch',[MovieController::class, 'searchBytitle']);
    Route::get('genresearch',[MovieController::class, 'searchBygenre']);
    Route::post('addmovie',[MovieController::class, 'addMovie']);
    Route::get('movierating',[MovieController::class, 'movieRating']);
});
