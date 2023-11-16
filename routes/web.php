<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\Helper;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    // return view('welcome');
    return Helper::unauthorized('Unauthorized');
})->name('login');

Route::get('stringPosition',[App\Http\Controllers\TestController::class,'index']);
Route::get('test',[App\Http\Controllers\TestController::class,'test']);


Route::get('transFileUpload',[App\Http\Controllers\v1\Cron\TransFileUploadController::class,'upload']);
