<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\PraiseController;

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
Route::post('signup', [AuthController::class, 'signUp']);//1
Route::post('login', [AuthController::class, 'login']);//6
Route::post('get-Otp', [AuthController::class, 'getOtp']);//2
Route::post('match-otp', [AuthController::class, 'matchOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
// Route::post('match-otp-forgot', [AuthController::class, 'matchOtpForgot']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/create-praise', [PraiseController::class,'createPraise']);//11
    Route::get('/get-received-praises', [PraiseController::class,'getReceivedPraises']);//8
    Route::get('/get-sent-praises', [PraiseController::class,'getSentPraises']);//7,12
    Route::post('/get-praises-by-id', [PraiseController::class,'getPraiseById']);//10
    Route::post('/update-status', [PraiseController::class,'updateStatus']);//9
    Route::get('/filters', [PraiseController::class,'filters']);//13
});
