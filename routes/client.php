<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('clients')->group(function () {
            Route::middleware(['auth:api'])->group(function () {
                Route::post('liveMFPortfolio',[App\Http\Controllers\v1\Client\LiveMFPController::class,'search']);
                Route::post('doNotShowFolio',[App\Http\Controllers\v1\Client\LiveMFPController::class,'doNotShowFolio']);
                Route::post('doNotShowFolioLock',[App\Http\Controllers\v1\Client\LiveMFPController::class,'doNotShowFolioLock']);
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
                Route::post('aumFundHouse',[App\Http\Controllers\v1\Client\AUMController::class,'search']);  // 
                Route::post('aumClient',[App\Http\Controllers\v1\Client\AUMController::class,'aumByClient']);  // 
                Route::post('aumFamily',[App\Http\Controllers\v1\Client\AUMController::class,'aumByFamily']);  // 
                Route::post('aumBranch',[App\Http\Controllers\v1\Client\AUMController::class,'aumBranch']);  // 
                Route::post('aumSegment',[App\Http\Controllers\v1\Client\AUMController::class,'aumSegment']);  // 
                Route::post('aumCityType',[App\Http\Controllers\v1\Client\AUMController::class,'aumCityType']);  // 
                Route::post('aumGrowth',[App\Http\Controllers\v1\Client\AUMController::class,'aumGrowth']);  // 
                // Route::post('aumTopClient',[App\Http\Controllers\v1\Client\AUMController::class,'aumTopClient']);  // 
            });
            Route::post('downloadValuation',[App\Http\Controllers\v1\Client\PDFController::class,'downloadValuation']);  // 
            Route::get('downloadValuation',[App\Http\Controllers\v1\Client\PDFController::class,'autoDownloadValuation']);  // 
                
            // Route::get('liveMFPortfolio1',[App\Http\Controllers\v1\Client\LiveMFPController::class,'search1']);
            Route::get('aum',[App\Http\Controllers\v1\Client\AUMController::class,'aumGrowth1']);
            // Route::get('aumtest',[App\Http\Controllers\v1\Client\AUMController::class,'aumtest']);
            // Route::any('aum1',[App\Http\Controllers\v1\Client\AUMController::class,'search1']);  // 
            Route::any('genpdf',[App\Http\Controllers\v1\Client\PDFController::class,'generatePDF']);  // 
            Route::any('testgenpdf',[App\Http\Controllers\v1\Client\PDFController::class,'generatePDFTest']);  // 
            
            Route::get('aum1',[App\Http\Controllers\v1\Client\AumCalculationController::class,'index']);  // 
            Route::get('aum10',[App\Http\Controllers\v1\Client\AumCalculationController::class,'test']);  // 
            // Route::get('aum2',[App\Http\Controllers\v1\Client\AUMController::class,'search']);  // 
            // Route::get('aum3',[App\Http\Controllers\v1\Client\AUMController::class,'search3']);  // 
            // Route::get('aumScheme1',[App\Http\Controllers\v1\Client\AUMController::class,'aumByScheme']);  // 
            // Route::get('aumClient1',[App\Http\Controllers\v1\Client\AUMController::class,'aumByClient']);  // 
            // Route::get('aumFamily1',[App\Http\Controllers\v1\Client\AUMController::class,'aumByFamily']);  // 
            // Route::get('currAum',[App\Http\Controllers\v1\Client\AumCalculationController::class,'currAum']);  // 
            

            
            Route::get('nse',[App\Http\Controllers\v1\Client\NSEController::class,'index']);  // 

            
            Route::get('bse',[App\Http\Controllers\v1\Client\BSEController::class,'index']);  // 


            Route::get('nftoperation',[App\Http\Controllers\v1\Operation\TestOperationController::class,'index']);  // 
            Route::get('deletetest',[App\Http\Controllers\v1\Operation\TestOperationController::class,'test']);  // 


            Route::get('showCurrAum1',[App\Http\Controllers\v1\Reports\HomeController::class,'currAum1']);  // current aum


        });
    // })
});