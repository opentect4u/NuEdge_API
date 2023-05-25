<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('comp')->group(function () {
            Route::get('type',[App\Http\Controllers\v1\CompMaster\CompTypeController::class,'index']);
            // Route::any('typeDetailSearch',[App\Http\Controllers\v1\CompMaster\CompTypeController::class,'searchDetails']);
            // Route::post('typeExport',[App\Http\Controllers\v1\CompMaster\CompTypeController::class,'export']);
            Route::post('typeAddEdit',[App\Http\Controllers\v1\CompMaster\CompTypeController::class,'createUpdate']);
            // Route::post('typeimport', [App\Http\Controllers\v1\CompMaster\CompTypeController::class,'import']);
            // Route::post('typeDelete', [App\Http\Controllers\v1\CompMaster\CompTypeController::class,'delete']);

            Route::get('profile',[App\Http\Controllers\v1\CompMaster\CompProfileController::class,'index']);  // show company profile also use for dropdown list
            Route::post('profileAddEdit',[App\Http\Controllers\v1\CompMaster\CompProfileController::class,'createUpdate']); // create and update company profile

            Route::get('director',[App\Http\Controllers\v1\CompMaster\DirectorDetailsController::class,'index']); // show director deatils also use for dropdown list
            Route::post('directorAddEdit',[App\Http\Controllers\v1\CompMaster\DirectorDetailsController::class,'createUpdate']); // create and update director deatils

        });
    // });
});