<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*****************for customer service related routes************************ */
Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('cus_service')->group(function () {
            Route::middleware(['auth:api'])->group(function () {
                Route::get('queryType',[App\Http\Controllers\v1\CusService\QueryTypeController::class,'index']); // show query type also use for dropdown list
                Route::post('queryTypeAddEdit',[App\Http\Controllers\v1\CusService\QueryTypeController::class,'createUpdate']); // create and update query type

                Route::get('querySubType',[App\Http\Controllers\v1\CusService\QuerySubTypeController::class,'index']); // show query sub type also use for dropdown list
                Route::post('querySubTypeAddEdit',[App\Http\Controllers\v1\CusService\QuerySubTypeController::class,'createUpdate']); // create and update query sub type
        
                Route::get('queryStatus',[App\Http\Controllers\v1\CusService\QueryStatusController::class,'index']); // show query status also use for dropdown list
                Route::post('queryStatusAddEdit',[App\Http\Controllers\v1\CusService\QueryStatusController::class,'createUpdate']); // create and update query status

                Route::get('queryNature',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'index']); // show query nature also use for dropdown list
                Route::post('queryNatureAddEdit',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'createUpdate']); // create and update query nature

                Route::get('queryGivenBy',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'indexGivenBy']); // show query given by also use for dropdown list
                Route::post('queryGivenByAddEdit',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'createUpdateGivenBy']); // create and update query given by

                Route::get('queryGivenThrough',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'indexGivenThrough']); // show query given through also use for dropdown list
                Route::post('queryGivenThroughAddEdit',[App\Http\Controllers\v1\CusService\QueryNatureController::class,'createUpdateGivenThrough']); // create and update query given through
                
                Route::get('getFolio',[App\Http\Controllers\v1\CusService\QueryController::class,'getFolio']); // show folio details
                Route::get('getFoliowiseProduct',[App\Http\Controllers\v1\CusService\QueryController::class,'getFoliowiseProduct']); // show folio wise product details
                Route::post('queryAdd',[App\Http\Controllers\v1\CusService\QueryController::class,'createUpdate']); // create and update query
                Route::post('queryShow',[App\Http\Controllers\v1\CusService\QueryController::class,'index']); // show all query
                Route::get('searchQueryId',[App\Http\Controllers\v1\CusService\QueryController::class,'search']); // search query by query id

                Route::post('queryInform',[App\Http\Controllers\v1\CusService\QueryController::class,'queryInform']); // inform query to client
                Route::post('addTatRemarks',[App\Http\Controllers\v1\CusService\QueryController::class,'addTATRemarks']); // add TAT remarks for query
                
                Route::get('users',[App\Http\Controllers\v1\CusService\UserController::class,'index']); // show users also use for dropdown list
                Route::get('searchClient',[App\Http\Controllers\v1\CusService\QueryController::class,'searchClient']); // search client by folio number or client name


                Route::get('index',[App\Http\Controllers\v1\CusService\IndexController::class,'index']); // show dashboard data
                Route::get('holiday',[App\Http\Controllers\v1\Master\HolidayController::class,'index']); // show holiday list

            });
            Route::post('downloadFile',[App\Http\Controllers\v1\CusService\QueryController::class,'downloadFile']); // download file from query click on email or message
            Route::post('queryShowDetails',[App\Http\Controllers\v1\CusService\QueryController::class,'showDetails']); // show query details by query id click on email or message
            Route::post('queryFeedback',[App\Http\Controllers\v1\CusService\QueryController::class,'feedback']); // add feedback for query click on email or message


            // Route::get('sendsms',[App\Http\Controllers\v1\CusService\QueryController::class,'sendSMS']);
            // Route::get('whatsapp',[App\Http\Controllers\v1\CusService\QueryController::class,'whatsapp']);
            // Route::get('sendemail',[App\Http\Controllers\v1\CusService\QueryController::class,'sendemail']);
            // Route::get('index1',[App\Http\Controllers\v1\CusService\IndexController::class,'index']);

        });
    // });
});