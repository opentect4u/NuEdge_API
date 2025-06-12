<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/***************************for live mf portfolio and aum related routes******************** */
Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('clients')->group(function () {
            Route::middleware(['auth:api'])->group(function () {
                Route::post('liveMFPortfolio',[App\Http\Controllers\v1\Client\LiveMFPController::class,'search']); // for live MF portfolio
                Route::post('doNotShowFolio',[App\Http\Controllers\v1\Client\LiveMFPController::class,'doNotShowFolio']); // for do not show portfolio
                Route::post('doNotShowFolioLock',[App\Http\Controllers\v1\Client\LiveMFPController::class,'doNotShowFolioLock']); // for do not show portfolio lock
                Route::get('liveMFShowDetails',[App\Http\Controllers\v1\Client\LiveMFPController::class,'showDetails']); // for live MF portfolio details
                Route::get('liveMFPortfolioDetails',[App\Http\Controllers\v1\Client\LiveMFPController::class,'searchDetails']); // for live MF portfolio details
                Route::post('liveMFPL',[App\Http\Controllers\v1\Client\LiveMFPLController::class,'search']); // for live MF protfolio profit loss
                Route::post('liveMFRecentTrans',[App\Http\Controllers\v1\Client\LiveMFPController::class,'recentTrans']); // for live MF recent transaction
                Route::post('liveMFRejectTrans',[App\Http\Controllers\v1\Client\LiveMFPController::class,'rejectTrans']); // for live MF reject transaction
                Route::post('liveMFSTW',[App\Http\Controllers\v1\Client\LiveMFSTWController::class,'search']);  // for STP && SWP && STP
                Route::post('liveMFUpcoming',[App\Http\Controllers\v1\Client\LiveMFSTWController::class,'upcomingTrans']);  // for STP && SWP && STP upcoming transaction
                Route::post('divHistory',[App\Http\Controllers\v1\Client\LiveMFPController::class,'divHistory']);  // for div History transaction
                
                Route::post('sendEmailWithLink',[App\Http\Controllers\v1\Client\PDFController::class,'sendEmailWithLink']);  // for send email with link

                
                Route::post('realisedCapitalGain',[App\Http\Controllers\v1\Client\CapitalGLController::class,'search']); // for realised capital gain
                Route::post('finYearWiseTrans',[App\Http\Controllers\v1\Client\CapitalGLController::class,'finWiseTrans']); // for financial year wise transaction
                Route::post('realisedDivHistory',[App\Http\Controllers\v1\Client\CapitalGLController::class,'divHistory']); // for realised divident history
                Route::post('aumFundHouse',[App\Http\Controllers\v1\Client\AUMController::class,'search']);  // for aum by fund house
                Route::post('aumClient',[App\Http\Controllers\v1\Client\AUMController::class,'aumByClient']);  // for aum by client
                Route::post('aumFamily',[App\Http\Controllers\v1\Client\AUMController::class,'aumByFamily']);  // for aum by family
                Route::post('aumBranch',[App\Http\Controllers\v1\Client\AUMController::class,'aumBranch']);  // for aum by branch
                Route::post('aumSegment',[App\Http\Controllers\v1\Client\AUMController::class,'aumSegment']);  // for aum by segment 
                Route::post('aumCityType',[App\Http\Controllers\v1\Client\AUMController::class,'aumCityType']);  // for aum by city type
                Route::post('aumGrowth',[App\Http\Controllers\v1\Client\AUMController::class,'aumGrowth']);  // for aum growth
                // Route::post('aumTopClient',[App\Http\Controllers\v1\Client\AUMController::class,'aumTopClient']);  // 
            });
            Route::post('downloadValuation',[App\Http\Controllers\v1\Client\PDFController::class,'downloadValuation']);  // for download valuation report
            Route::get('downloadValuation',[App\Http\Controllers\v1\Client\PDFController::class,'autoDownloadValuation']);  // for auto download valuation report
                
            // Route::get('liveMFPortfolio1',[App\Http\Controllers\v1\Client\LiveMFPController::class,'search1']);
            // Route::get('aum',[App\Http\Controllers\v1\Client\AUMController::class,'aumGrowth1']); 
            // Route::get('aumtest',[App\Http\Controllers\v1\Client\AUMController::class,'aumtest']);
            // Route::any('aum1',[App\Http\Controllers\v1\Client\AUMController::class,'search1']);  // 
            // Route::any('genpdf',[App\Http\Controllers\v1\Client\PDFController::class,'generatePDF']);  // 
            // Route::any('testgenpdf',[App\Http\Controllers\v1\Client\PDFController::class,'generatePDFTest']);  // 
            
            Route::get('aum1',[App\Http\Controllers\v1\Client\AumCalculationController::class,'index']);  //  for manual aum calculation
            Route::post('mytesting',[App\Http\Controllers\v1\Client\AumCalculationController::class,'mytesting']);  //  for testing manual aum calculation
            // Route::get('aum10',[App\Http\Controllers\v1\Client\AumCalculationController::class,'test']);  // 
            // Route::get('aum2',[App\Http\Controllers\v1\Client\AUMController::class,'search']);  // 
            // Route::get('aum3',[App\Http\Controllers\v1\Client\AUMController::class,'search3']);  // 
            // Route::get('aumScheme1',[App\Http\Controllers\v1\Client\AUMController::class,'aumByScheme']);  // 
            // Route::get('aumClient1',[App\Http\Controllers\v1\Client\AUMController::class,'aumByClient']);  // 
            // Route::get('aumFamily1',[App\Http\Controllers\v1\Client\AUMController::class,'aumByFamily']);  // 
            // Route::get('currAum',[App\Http\Controllers\v1\Client\AumCalculationController::class,'currAum']);  // 
            

            
            // Route::get('nse',[App\Http\Controllers\v1\Client\NSEController::class,'index']);  // 

            
            // Route::get('bse',[App\Http\Controllers\v1\Client\BSEController::class,'index']);  // 


            // Route::get('nftoperation',[App\Http\Controllers\v1\Operation\TestOperationController::class,'index']);  // 
            // Route::get('deletetest',[App\Http\Controllers\v1\Operation\TestOperationController::class,'test']);  // 


            // Route::get('showCurrAum1',[App\Http\Controllers\v1\Reports\HomeController::class,'currAum1']);  // current aum


        });
    // })
});