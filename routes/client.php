<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('clients')->group(function () {
            Route::middleware(['auth:api'])->group(function () {
                Route::post('liveMFPortfolio',[App\Http\Controllers\v1\Client\LiveMFPController::class,'search']);
                Route::get('liveMFShowDetails',[App\Http\Controllers\v1\Client\LiveMFPController::class,'showDetails']);
                Route::get('liveMFPortfolioDetails',[App\Http\Controllers\v1\Client\LiveMFPController::class,'searchDetails']);
                Route::post('liveMFPL',[App\Http\Controllers\v1\Client\LiveMFPLController::class,'search']);
                Route::post('liveMFRecentTrans',[App\Http\Controllers\v1\Client\LiveMFPController::class,'recentTrans']);
                Route::post('liveMFSTW',[App\Http\Controllers\v1\Client\LiveMFSTWController::class,'search']);  // for STP && SWP && STP
            });
            Route::get('liveMFPortfolio1',[App\Http\Controllers\v1\Client\LiveMFPController::class,'search1']);

        });
    // })
});