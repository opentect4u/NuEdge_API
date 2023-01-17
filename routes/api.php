<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::get('getip',[App\Http\Controllers\v1\TestController::class,'ShowIp']);
        Route::get('index1',[App\Http\Controllers\v1\TestController::class,'index1']);
        Route::get('index2',[App\Http\Controllers\v1\TestController::class,'index2']);


        Route::post('login',[App\Http\Controllers\v1\TestController::class,'login']);


        Route::get('rnt',[App\Http\Controllers\v1\Master\RNTController::class,'index']);
        Route::post('rntAddEdit',[App\Http\Controllers\v1\Master\RNTController::class,'createUpdate']);

        Route::get('product',[App\Http\Controllers\v1\Master\ProductController::class,'index']);
        Route::post('productAddEdit',[App\Http\Controllers\v1\Master\ProductController::class,'createUpdate']);

        Route::get('amc',[App\Http\Controllers\v1\Master\AMCController::class,'index']);
        Route::post('amcAddEdit',[App\Http\Controllers\v1\Master\AMCController::class,'createUpdate']);

        Route::get('branch',[App\Http\Controllers\v1\Master\BranchController::class,'index']);
        Route::post('branchAddEdit',[App\Http\Controllers\v1\Master\BranchController::class,'createUpdate']);

        Route::get('category',[App\Http\Controllers\v1\Master\CategoryController::class,'index']);
        Route::post('categoryAddEdit',[App\Http\Controllers\v1\Master\CategoryController::class,'createUpdate']);

        Route::get('subcategory',[App\Http\Controllers\v1\Master\SubcategoryController::class,'index']);
        Route::post('subcategoryAddEdit',[App\Http\Controllers\v1\Master\SubcategoryController::class,'createUpdate']);

        Route::get('formtype',[App\Http\Controllers\v1\Master\FormTypeController::class,'index']);
        Route::post('formtypeAddEdit',[App\Http\Controllers\v1\Master\FormTypeController::class,'createUpdate']);

        Route::get('transctiontype',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'index']);
        Route::post('transctiontypeAddEdit',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'createUpdate']);

        Route::get('transction',[App\Http\Controllers\v1\Master\TransctionController::class,'index']);
        Route::post('transctionAddEdit',[App\Http\Controllers\v1\Master\TransctionController::class,'createUpdate']);

        Route::get('scheme',[App\Http\Controllers\v1\Master\SchemeController::class,'index']);
        Route::post('schemeAddEdit',[App\Http\Controllers\v1\Master\SchemeController::class,'createUpdate']);

        Route::get('depositbank',[App\Http\Controllers\v1\Master\DepositBankController::class,'index']);
        Route::post('depositbankAddEdit',[App\Http\Controllers\v1\Master\DepositBankController::class,'createUpdate']);

        Route::get('documenttype',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'index']);
        Route::post('documenttypeAddEdit',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'createUpdate']);

        Route::get('employee',[App\Http\Controllers\v1\Master\EmployeeController::class,'index']);
        Route::post('employeeAdd',[App\Http\Controllers\v1\Master\EmployeeController::class,'create']);
        Route::post('employeeEdit',[App\Http\Controllers\v1\Master\EmployeeController::class,'update']);

        Route::get('subbroker',[App\Http\Controllers\v1\Master\SubBrokerController::class,'index']);
        Route::post('subbrokerAddEdit',[App\Http\Controllers\v1\Master\SubBrokerController::class,'createUpdate']);

        Route::get('states',[App\Http\Controllers\v1\Master\StateController::class,'index']);



        // dropdown onchange routes start
        Route::get('amcUsingPro',[App\Http\Controllers\v1\Master\CommonController::class,'showAMC']);
        Route::get('catUsingPro',[App\Http\Controllers\v1\Master\CommonController::class,'showCategory']);
        Route::get('subcatUsingPro',[App\Http\Controllers\v1\Master\CommonController::class,'showSubCategory']);
        // dropdown onchange routes end
        
        // Route::get('depositbank',[App\Http\Controllers\v1\Master\DepositBankController::class,'index']);
        // Route::post('depositbankAddEdit',[App\Http\Controllers\v1\Master\DepositBankController::class,'createUpdate']);

        // operations routes start 
        Route::get('formreceived',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'index']);
        Route::get('formreceivedshow',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'createShow']);
        Route::post('formreceivedAdd',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'create']);

        Route::get('formtypeUsingPro',[App\Http\Controllers\v1\Operation\CommonController::class,'showFormType']);
        Route::get('showsubbroker',[App\Http\Controllers\v1\Operation\CommonController::class,'ShowSubBroker']);
        Route::get('subbrocodeUsingarn',[App\Http\Controllers\v1\Operation\CommonController::class,'showSubBrokerCode']);
        Route::get('showTransInFormRec',[App\Http\Controllers\v1\Operation\CommonController::class,'showTransInFormRec']);

        Route::get('client',[App\Http\Controllers\v1\Master\ClientController::class,'index']);
        Route::post('clientAddEdit',[App\Http\Controllers\v1\Master\ClientController::class,'createUpdate']);

        Route::get('documentsearch',[App\Http\Controllers\v1\Master\DocumentController::class,'search']);
        Route::get('document',[App\Http\Controllers\v1\Master\DocumentController::class,'index']);
        Route::get('documentshowEdit',[App\Http\Controllers\v1\Master\DocumentController::class,'Edit']);
        Route::post('documentAdd',[App\Http\Controllers\v1\Master\DocumentController::class,'create']);
        Route::post('documentEdit',[App\Http\Controllers\v1\Master\DocumentController::class,'update']);


        Route::get('kyc',[App\Http\Controllers\v1\Operation\KYCController::class,'index']);
        Route::get('kycshowadd',[App\Http\Controllers\v1\Operation\KYCController::class,'showAdd']);
        Route::post('kycAddEdit',[App\Http\Controllers\v1\Operation\KYCController::class,'createUpdate']);


        Route::get('showTrans',[App\Http\Controllers\v1\Operation\CommonController::class,'showTrans']);

        Route::get('financial',[App\Http\Controllers\v1\Operation\FinancialController::class,'index']);
        Route::post('financialAddEdit',[App\Http\Controllers\v1\Operation\FinancialController::class,'createUpdate']);

        // operations routes end 

        // Route::post('index1',[App\Http\Controllers\v1\Master\TestController::class,'index1']);
    // });
});