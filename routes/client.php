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
                Route::post('liveMFRejectTrans',[App\Http\Controllers\v1\Client\LiveMFPController::class,'rejectTrans']);
                Route::post('liveMFSTW',[App\Http\Controllers\v1\Client\LiveMFSTWController::class,'search']);  // for STP && SWP && STP
                Route::post('liveMFUpcoming',[App\Http\Controllers\v1\Client\LiveMFSTWController::class,'upcomingTrans']);  // for STP && SWP && STP upcoming transaction
                Route::post('divHistory',[App\Http\Controllers\v1\Client\LiveMFPController::class,'divHistory']);  // for div History transaction
                
                Route::post('sendEmailWithLink',[App\Http\Controllers\v1\Client\PDFController::class,'sendEmailWithLink']);  // 

                
                Route::post('realisedCapitalGain',[App\Http\Controllers\v1\Client\CapitalGLController::class,'search']);
                Route::post('finYearWiseTrans',[App\Http\Controllers\v1\Client\CapitalGLController::class,'finWiseTrans']);
                Route::post('realisedDivHistory',[App\Http\Controllers\v1\Client\CapitalGLController::class,'divHistory']);
                Route::post('aum',[App\Http\Controllers\v1\Client\AUMController::class,'search']);  // 
            });
            Route::post('downloadValuation',[App\Http\Controllers\v1\Client\PDFController::class,'downloadValuation']);  // 
            Route::get('downloadValuation',[App\Http\Controllers\v1\Client\PDFController::class,'autoDownloadValuation']);  // 
                
            // Route::get('liveMFPortfolio1',[App\Http\Controllers\v1\Client\LiveMFPController::class,'search1']);
            // Route::any('aum1',[App\Http\Controllers\v1\Client\AUMController::class,'search1']);  // 
            Route::any('genpdf',[App\Http\Controllers\v1\Client\PDFController::class,'generatePDF']);  // 
            Route::any('testgenpdf',[App\Http\Controllers\v1\Client\PDFController::class,'generatePDFTest']);  // 
            
        });
    // })
});