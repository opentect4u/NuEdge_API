<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('cusService')->group(function () {
            Route::middleware(['auth:api'])->group(function () {
                Route::get('queryType',[App\Http\Controllers\v1\CusService\QueryTypeController::class,'index']);
                Route::post('queryTypeAddEdit',[App\Http\Controllers\v1\CusService\QueryTypeController::class,'createUpdate']);

                Route::get('querySubType',[App\Http\Controllers\v1\CusService\QuerySubTypeController::class,'index']);
                Route::post('querySubTypeAddEdit',[App\Http\Controllers\v1\CusService\QuerySubTypeController::class,'createUpdate']);
        
                Route::get('queryStatus',[App\Http\Controllers\v1\CusService\QueryStatusController::class,'index']);
                Route::post('queryStatusAddEdit',[App\Http\Controllers\v1\CusService\QueryStatusController::class,'createUpdate']);

                Route::get('queryNature',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'index']);
                Route::post('queryNatureAddEdit',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'createUpdate']);

                Route::get('queryGivenBy',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'indexGivenBy']);
                Route::post('queryGivenByAddEdit',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'createUpdateGivenBy']);

                Route::get('queryGivenThrough',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'indexGivenThrough']);
                Route::post('queryGivenThroughAddEdit',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'createUpdateGivenThrough']);
                
                Route::get('getFolio',[App\Http\Controllers\v1\CusService\QueryController::class,'getFolio']);
                Route::get('getFoliowiseProduct',[App\Http\Controllers\v1\CusService\QueryController::class,'getFoliowiseProduct']);
                Route::post('queryAdd',[App\Http\Controllers\v1\CusService\QueryController::class,'createUpdate']);
                Route::post('queryShow',[App\Http\Controllers\v1\CusService\QueryController::class,'index']);

            });
        });
    // });
});