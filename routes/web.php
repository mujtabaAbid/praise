<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

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

Route::post('/admin-login', [\App\Http\Controllers\admin\AuthController::class, 'admin_login'])->name('admin_login');
Route::get('/admin-logout', [\App\Http\Controllers\admin\AuthController::class, 'admin_logout'])->name('admin_logout');

Route::get('/', function () {
    return view('login');
})->name('login');



Route::group(['prefix' => 'dashboard', 'middleware' => 'auth:admin'], function () {
    Route::get('/', function () {
        return view('index');
    })->name('dashboard');
    Route::get('/users', [DashboardController::class, 'users'])->name('users');
    Route::post('/get-users', [DashboardController::class, 'getUser'])->name('get.users');

});



