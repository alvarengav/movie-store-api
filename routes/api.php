<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotResetPasswordController;
use App\Http\Controllers\MovieBuyController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieImageController;
use App\Http\Controllers\MovieLikesController;
use App\Http\Controllers\MovieLogController;
use App\Http\Controllers\MovieRentController;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\RentsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserMovieRentController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VerifyEmailController;
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

Route::group([
    'prefix' => 'auth'
], function ($router) {

    //auth endpoints
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});


//endpoints
Route::get('/movies/search', [MovieController::class, 'search'])->name('movies.search');
Route::get('/movies/logs', [MovieLogController::class, 'index']);
Route::get('/movies/logs/{id}', [MovieLogController::class, 'show']);
Route::apiResource('/movies', MovieController::class);
Route::apiResource('/movies.images', MovieImageController::class)->except(['update']);
Route::apiResource('/movies.likes', MovieLikesController::class)
    ->only(['index', 'store'])
    ->middleware('auth:api');
Route::middleware(['auth:api'])->group(function () {
    Route::post('/movies/{movie}/buy', MovieBuyController::class);
    Route::post('/movies/{movie}/rent', [MovieRentController::class, 'newRentMovie']);
    Route::get('/users/{user}/rents', [UserMovieRentController::class, 'moviesRent']);
    Route::match(['put', 'patch'], '/rents/{rent}', [MovieRentController::class, 'returnMovie']);
    Route::apiResource('/rents', RentsController::class)->only(['index', 'show']);
    Route::apiResource('/sales', SalesController::class)->only(['index', 'show']);
    Route::patch('/users/{user}', [UsersController::class, 'updateRole'])->name('users.updateRole');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}', [UsersController::class, 'updatePassword'])->name('users.updatePassword');
    Route::apiResource('/users', UsersController::class)->except(['update']);
});
Route::post('/users/register', RegisterUserController::class)->name('users.register');
Route::get('/users/email/verify/{id}/{hash}', VerifyEmailController::class)->middleware(['auth:api', 'signed'])->name('verification.verify');
Route::post('/auth/forgot-password', [ForgotResetPasswordController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [ForgotResetPasswordController::class, 'resetPassword']);
