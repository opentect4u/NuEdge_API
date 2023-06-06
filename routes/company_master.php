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

            Route::get('bank',[App\Http\Controllers\v1\CompMaster\BankController::class,'index']); // show bank deatils also use for dropdown list
            Route::post('bankAddEdit',[App\Http\Controllers\v1\CompMaster\BankController::class,'createUpdate']); // create and update bank deatils

            Route::get('documentLocker',[App\Http\Controllers\v1\CompMaster\DocumentLockerController::class,'index']); // show document locker also use for dropdown list
            Route::post('documentLockerAddEdit',[App\Http\Controllers\v1\CompMaster\DocumentLockerController::class,'createUpdate']); // create and update document locker

            Route::get('product',[App\Http\Controllers\v1\CompMaster\CompProductsController::class,'index']); // show product deatils also use for dropdown list
            Route::post('productAddEdit',[App\Http\Controllers\v1\CompMaster\CompProductsController::class,'createUpdate']); // create and update product deatils

            Route::get('license',[App\Http\Controllers\v1\CompMaster\LicenseDetailsController::class,'index']); // show product deatils also use for dropdown list
            Route::post('licenseAddEdit',[App\Http\Controllers\v1\CompMaster\LicenseDetailsController::class,'createUpdate']); // create and update product deatils

            Route::get('loginpass',[App\Http\Controllers\v1\CompMaster\LoginPassLockerController::class,'index']); // show product deatils also use for dropdown list
            Route::post('loginpassAddEdit',[App\Http\Controllers\v1\CompMaster\LoginPassLockerController::class,'createUpdate']); // create and update product deatils

            Route::get('partnership',[App\Http\Controllers\v1\CompMaster\PartnershipDetailsController::class,'index']); // show product deatils also use for dropdown list
            Route::post('partnershipAddEdit',[App\Http\Controllers\v1\CompMaster\PartnershipDetailsController::class,'createUpdate']); // create and update product deatils

            Route::get('sharedHolder',[App\Http\Controllers\v1\CompMaster\SharedHolderController::class,'index']); // show product deatils also use for dropdown list
            Route::post('sharedHolderAddEdit',[App\Http\Controllers\v1\CompMaster\SharedHolderController::class,'createUpdate']); // create and update product deatils

        });
    // });
});