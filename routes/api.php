<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

});

Route::middleware('auth:api')->group(function () {
    
    Route::prefix('users')->group(function () {
        Route::get('', [UserController::class, 'findAll']);
        Route::post('me', [UserController::class, 'me']);
        Route::delete('me', [UserController::class, 'deleteMyAccount']);
        
        Route::post('role', [UserController::class, 'addRole']);
        Route::delete('role', [UserController::class, 'removeRole']);


        Route::put('profile/user', [UserController::class, 'createUserProfile']);
        Route::put('profile/rider', [UserController::class, 'createRiderProfile']);

        Route::post('{user}/score', [UserController::class, 'giveScoreToRider']);
        Route::put('{user}/status', [UserController::class, 'updateRiderStatus']);

        Route::delete('{user}', [UserController::class, 'destroy']);
    });
    
});