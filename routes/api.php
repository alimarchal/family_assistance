<?php

use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\OtpApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/forgot-password', [UserController::class, 'forgot_password'])->middleware('guest');


});

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/user/{id}', [UserController::class, 'show']);
    Route::put('/user/{id}', [UserController::class, 'update']);

    Route::post('/otp-generate', [OtpApiController::class, 'otpGenerate']);
    Route::get('/otp/{id}', [OtpApiController::class, 'show']);
    Route::get('/otp/{id}/latest', [OtpApiController::class, 'showLatest']);
    Route::get('/otp/{showUserParentId}', [OtpApiController::class, 'showUserParentId']);
});
