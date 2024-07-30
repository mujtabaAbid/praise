<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\PraiseController;
use Illuminate\Support\Facades\Auth;

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

//auth controller
Route::post('signup', [AuthController::class, 'signUp']);//1
Route::post('login', [AuthController::class, 'login']);//6
Route::post('social-login', [AuthController::class, 'socialLogin']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::post('get-Otp', [AuthController::class, 'getOtp']);//2
Route::post('match-otp', [AuthController::class, 'matchOtp']);

Route::get('countries', [AuthController::class, 'countries']);
Route::post('state', [AuthController::class, 'state']);
Route::post('cities', [AuthController::class, 'cities']);

Route::post('/filters', [PraiseController::class,'praisesFilter']);//13

// Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
// Route::post('match-otp-forgot', [AuthController::class, 'matchOtpForgot']);


Route::middleware('auth:sanctum')->group(function () {


    Route::post('/create-praise', [PraiseController::class,'createPraise']);//11
    Route::get('/get-received-praises', [PraiseController::class,'getReceivedPraises']);//8
    Route::get('/get-sent-praises', [PraiseController::class,'getSentPraises']);//7,12
    Route::post('/get-praises-by-id', [PraiseController::class,'getPraiseById']);//10
    Route::post('/update-status', [PraiseController::class,'updateStatus']);//9
    
    //to view other user details by passing id
    Route::post('/user-details', [PraiseController::class,'userDetails']);//9
    
    Route::get('/user-profile', [PraiseController::class,'userProfile']);//9

    Route::post('/search', [PraiseController::class, 'search']);
    Route::post('/filters', [AuthController::class, 'filters']);
    
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/specific-praise-senders', [PraiseController::class, 'specificPraiseSendersDetails']);


    Route::get('/all-users', [PraiseController::class,'allUsers']);
    Route::get('/all-users-paginated', [PraiseController::class,'allUsersWithPagination']);
    Route::get('/praisy-category', [PraiseController::class,'praisyCategory']);//9
    
});
