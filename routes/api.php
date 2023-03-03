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

        Route::get('mdparams',[App\Http\Controllers\v1\CommonController::class,'CommonParamValue']);


        Route::get('rnt',[App\Http\Controllers\v1\Master\RNTController::class,'index']);
        Route::get('rntDetailSearch',[App\Http\Controllers\v1\Master\RNTController::class,'searchDetails']);
        Route::post('rntDetailSearch',[App\Http\Controllers\v1\Master\RNTController::class,'searchDetails']);
        Route::post('rntExport',[App\Http\Controllers\v1\Master\RNTController::class,'export']);
        Route::post('rntAddEdit',[App\Http\Controllers\v1\Master\RNTController::class,'createUpdate']);
        Route::post('rntimport', [App\Http\Controllers\v1\Master\RNTController::class,'import']);

        Route::get('product',[App\Http\Controllers\v1\Master\ProductController::class,'index']);
        Route::post('productAddEdit',[App\Http\Controllers\v1\Master\ProductController::class,'createUpdate']);

        Route::get('amc',[App\Http\Controllers\v1\Master\AMCController::class,'index']);
        Route::get('amcDetailSearch',[App\Http\Controllers\v1\Master\AMCController::class,'searchDetails']);
        Route::post('amcDetailSearch',[App\Http\Controllers\v1\Master\AMCController::class,'searchDetails']);
        Route::post('amcExport',[App\Http\Controllers\v1\Master\AMCController::class,'export']);
        Route::post('amcAddEdit',[App\Http\Controllers\v1\Master\AMCController::class,'createUpdate']);
        Route::post('amcimport', [App\Http\Controllers\v1\Master\AMCController::class,'import']);

        Route::get('branch',[App\Http\Controllers\v1\Master\BranchController::class,'index']);
        Route::post('branchAddEdit',[App\Http\Controllers\v1\Master\BranchController::class,'createUpdate']);

        Route::get('plan',[App\Http\Controllers\v1\Master\PlanController::class,'index']);
        Route::get('planDetailSearch',[App\Http\Controllers\v1\Master\PlanController::class,'searchDetails']);
        Route::post('planDetailSearch',[App\Http\Controllers\v1\Master\PlanController::class,'searchDetails']);
        Route::post('planExport',[App\Http\Controllers\v1\Master\PlanController::class,'export']);
        Route::post('planAddEdit',[App\Http\Controllers\v1\Master\PlanController::class,'createUpdate']);
        Route::post('planimport', [App\Http\Controllers\v1\Master\PlanController::class,'import']);

        Route::get('option',[App\Http\Controllers\v1\Master\OptionController::class,'index']);
        Route::get('optionDetailSearch',[App\Http\Controllers\v1\Master\OptionController::class,'searchDetails']);
        Route::post('optionDetailSearch',[App\Http\Controllers\v1\Master\OptionController::class,'searchDetails']);
        Route::post('optionExport',[App\Http\Controllers\v1\Master\OptionController::class,'export']);
        Route::post('optionAddEdit',[App\Http\Controllers\v1\Master\OptionController::class,'createUpdate']);
        Route::post('optionimport', [App\Http\Controllers\v1\Master\OptionController::class,'import']);

        Route::get('category',[App\Http\Controllers\v1\Master\CategoryController::class,'index']);
        Route::get('categoryDetailSearch',[App\Http\Controllers\v1\Master\CategoryController::class,'searchDetails']);
        Route::post('categoryDetailSearch',[App\Http\Controllers\v1\Master\CategoryController::class,'searchDetails']);
        Route::post('categoryExport',[App\Http\Controllers\v1\Master\CategoryController::class,'export']);
        Route::post('categoryAddEdit',[App\Http\Controllers\v1\Master\CategoryController::class,'createUpdate']);
        Route::post('categoryimport', [App\Http\Controllers\v1\Master\CategoryController::class,'import']);

        Route::get('subcategory',[App\Http\Controllers\v1\Master\SubcategoryController::class,'index']);
        Route::get('subcategoryDetailSearch',[App\Http\Controllers\v1\Master\SubcategoryController::class,'searchDetails']);
        Route::post('subcategoryDetailSearch',[App\Http\Controllers\v1\Master\SubcategoryController::class,'searchDetails']);
        Route::post('subcategoryExport',[App\Http\Controllers\v1\Master\SubcategoryController::class,'export']);
        Route::post('subcategoryAddEdit',[App\Http\Controllers\v1\Master\SubcategoryController::class,'createUpdate']);
        Route::post('subcategoryimport', [App\Http\Controllers\v1\Master\SubcategoryController::class,'import']);

        Route::get('formtype',[App\Http\Controllers\v1\Master\FormTypeController::class,'index']);
        Route::post('formtypeAddEdit',[App\Http\Controllers\v1\Master\FormTypeController::class,'createUpdate']);

        Route::get('transctiontype',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'index']);
        Route::get('transctiontypeSearch',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'searchDetails']);
        Route::post('transctiontypeSearch',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'searchDetails']);
        Route::post('transctiontypeExport',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'export']);
        Route::post('transctiontypeAddEdit',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'createUpdate']);

        Route::get('transction',[App\Http\Controllers\v1\Master\TransctionController::class,'index']);
        Route::get('transctionSearch',[App\Http\Controllers\v1\Master\TransctionController::class,'searchDetails']);
        Route::post('transctionSearch',[App\Http\Controllers\v1\Master\TransctionController::class,'searchDetails']);
        Route::post('transctionExport',[App\Http\Controllers\v1\Master\TransctionController::class,'export']);
        Route::post('transctionAddEdit',[App\Http\Controllers\v1\Master\TransctionController::class,'createUpdate']);

        Route::get('sipType',[App\Http\Controllers\v1\Master\SIPTypeController::class,'index']);
        Route::get('sipTypeSearch',[App\Http\Controllers\v1\Master\SIPTypeController::class,'searchDetails']);
        Route::post('sipTypeSearch',[App\Http\Controllers\v1\Master\SIPTypeController::class,'searchDetails']);
        Route::post('sipTypeExport',[App\Http\Controllers\v1\Master\SIPTypeController::class,'export']);
        Route::post('sipTypeAddEdit',[App\Http\Controllers\v1\Master\SIPTypeController::class,'createUpdate']);

        Route::get('scheme',[App\Http\Controllers\v1\Master\SchemeController::class,'index']);
        Route::get('schemeDetailSearch',[App\Http\Controllers\v1\Master\SchemeController::class,'searchDetails']);
        Route::post('schemeDetailSearch',[App\Http\Controllers\v1\Master\SchemeController::class,'searchDetails']);
        Route::post('schemeExport',[App\Http\Controllers\v1\Master\SchemeController::class,'export']);
        Route::post('schemeAddEdit',[App\Http\Controllers\v1\Master\SchemeController::class,'createUpdate']);
        Route::post('schemeimport', [App\Http\Controllers\v1\Master\SchemeController::class,'import']);

        Route::get('depositbank',[App\Http\Controllers\v1\Master\DepositBankController::class,'index']);
        Route::get('depositbankDetailSearch',[App\Http\Controllers\v1\Master\DepositBankController::class,'searchDetails']);
        Route::post('depositbankDetailSearch',[App\Http\Controllers\v1\Master\DepositBankController::class,'searchDetails']);
        Route::post('depositbankExport',[App\Http\Controllers\v1\Master\DepositBankController::class,'export']);
        Route::post('depositbankAddEdit',[App\Http\Controllers\v1\Master\DepositBankController::class,'createUpdate']);
        Route::post('depositbankimport', [App\Http\Controllers\v1\Master\DepositBankController::class,'import']);

        Route::get('documenttype',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'index']);
        Route::get('documenttypeDetailSearch',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'searchDetails']);
        Route::post('documenttypeDetailSearch',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'searchDetails']);
        Route::post('documenttypeExport',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'export']);
        Route::post('documenttypeAddEdit',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'createUpdate']);
        Route::post('documenttypeimport',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'import']);

        Route::get('employee',[App\Http\Controllers\v1\Master\EmployeeController::class,'index']);
        Route::post('employeeAdd',[App\Http\Controllers\v1\Master\EmployeeController::class,'create']);
        Route::post('employeeEdit',[App\Http\Controllers\v1\Master\EmployeeController::class,'update']);

        Route::get('subbroker',[App\Http\Controllers\v1\Master\SubBrokerController::class,'index']);
        Route::post('subbrokerAddEdit',[App\Http\Controllers\v1\Master\SubBrokerController::class,'createUpdate']);

        Route::get('states',[App\Http\Controllers\v1\Master\StateController::class,'index']);
        Route::get('districts',[App\Http\Controllers\v1\Master\DistrictController::class,'index']);
        Route::get('city',[App\Http\Controllers\v1\Master\CityController::class,'index']);

        Route::get('swpType',[App\Http\Controllers\v1\Master\SWPTypeController::class,'index']);
        Route::get('swpTypeSearch',[App\Http\Controllers\v1\Master\SWPTypeController::class,'searchDetails']);
        Route::post('swpTypeSearch',[App\Http\Controllers\v1\Master\SWPTypeController::class,'searchDetails']);
        Route::post('swpTypeExport',[App\Http\Controllers\v1\Master\SWPTypeController::class,'export']);
        Route::post('swpTypeAddEdit',[App\Http\Controllers\v1\Master\SWPTypeController::class,'createUpdate']);

        Route::get('stpType',[App\Http\Controllers\v1\Master\STPTypeController::class,'index']);
        Route::get('stpTypeSearch',[App\Http\Controllers\v1\Master\STPTypeController::class,'searchDetails']);
        Route::post('stpTypeSearch',[App\Http\Controllers\v1\Master\STPTypeController::class,'searchDetails']);
        Route::post('stpTypeExport',[App\Http\Controllers\v1\Master\STPTypeController::class,'export']);
        Route::post('stpTypeAddEdit',[App\Http\Controllers\v1\Master\STPTypeController::class,'createUpdate']);

        Route::get('clientType',[App\Http\Controllers\v1\Master\ClientTypeController::class,'index']);
        Route::get('clientTypeSearch',[App\Http\Controllers\v1\Master\ClientTypeController::class,'searchDetails']);
        Route::post('clientTypeSearch',[App\Http\Controllers\v1\Master\ClientTypeController::class,'searchDetails']);
        Route::post('clientTypeExport',[App\Http\Controllers\v1\Master\ClientTypeController::class,'export']);
        Route::post('clientTypeAddEdit',[App\Http\Controllers\v1\Master\ClientTypeController::class,'createUpdate']);

        Route::get('email',[App\Http\Controllers\v1\Master\EmailController::class,'index']);
        Route::get('emailSearch',[App\Http\Controllers\v1\Master\EmailController::class,'searchDetails']);
        Route::post('emailSearch',[App\Http\Controllers\v1\Master\EmailController::class,'searchDetails']);
        Route::post('emailAddEdit',[App\Http\Controllers\v1\Master\EmailController::class,'createUpdate']);
        Route::post('emailExport',[App\Http\Controllers\v1\Master\EmailController::class,'export']);
        Route::post('emailAddEdit',[App\Http\Controllers\v1\Master\EmailController::class,'createUpdate']);


        // dropdown onchange routes start
        Route::get('amcUsingPro',[App\Http\Controllers\v1\Master\CommonController::class,'showAMC']);
        Route::get('catUsingPro',[App\Http\Controllers\v1\Master\CommonController::class,'showCategory']);
        Route::get('subcatUsingPro',[App\Http\Controllers\v1\Master\CommonController::class,'showSubCategory']);
        // dropdown onchange routes end
        
        // Route::get('depositbank',[App\Http\Controllers\v1\Master\DepositBankController::class,'index']);
        // Route::post('depositbankAddEdit',[App\Http\Controllers\v1\Master\DepositBankController::class,'createUpdate']);

        // ================ operations routes start =====================

        Route::get('formreceived',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'index']);
        Route::get('formreceivedshow',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'createShow']);
        Route::post('formreceivedAdd',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'create']);
        Route::post('formreceivedEdit',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'update']);
        Route::post('formreceivedDelete',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'delete']);
        Route::get('formreceivedDetailSearch',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'searchDetails']);
        Route::post('formreceivedDetailSearch',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'searchDetails']);
        Route::post('formreceivedExport',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'export']);

        Route::get('formtypeUsingPro',[App\Http\Controllers\v1\Operation\CommonController::class,'showFormType']);
        Route::get('showsubbroker',[App\Http\Controllers\v1\Operation\CommonController::class,'ShowSubBroker']);
        Route::get('subbrocodeUsingarn',[App\Http\Controllers\v1\Operation\CommonController::class,'showSubBrokerCode']);
        Route::get('showTransInFormRec',[App\Http\Controllers\v1\Operation\CommonController::class,'showTransInFormRec']);

        Route::get('client',[App\Http\Controllers\v1\Master\ClientController::class,'index']);
        Route::post('clientAddEdit',[App\Http\Controllers\v1\Master\ClientController::class,'createUpdate']);
        Route::post('clientimport',[App\Http\Controllers\v1\Master\ClientController::class,'import']);
        Route::get('clientDetailSearch',[App\Http\Controllers\v1\Master\ClientController::class,'searchDetails']);
        Route::post('clientDetailSearch',[App\Http\Controllers\v1\Master\ClientController::class,'searchDetails']);
        Route::post('clientExport',[App\Http\Controllers\v1\Master\ClientController::class,'export']);
        
        Route::get('documentsearch',[App\Http\Controllers\v1\Master\DocumentController::class,'search']);
        Route::get('document',[App\Http\Controllers\v1\Master\DocumentController::class,'index']);
        Route::get('documentshowEdit',[App\Http\Controllers\v1\Master\DocumentController::class,'Edit']);
        Route::post('documentAdd',[App\Http\Controllers\v1\Master\DocumentController::class,'create']);
        Route::post('documentEdit',[App\Http\Controllers\v1\Master\DocumentController::class,'update']);
        Route::post('documentimport',[App\Http\Controllers\v1\Master\DocumentController::class,'import']);


        Route::get('kyc',[App\Http\Controllers\v1\Operation\KYCController::class,'index']);
        Route::get('kycshowadd',[App\Http\Controllers\v1\Operation\KYCController::class,'showAdd']);
        Route::post('kycAddEdit',[App\Http\Controllers\v1\Operation\KYCController::class,'createUpdate']);
        Route::get('kycDetailSearch',[App\Http\Controllers\v1\Operation\KYCController::class,'searchDetails']);
        Route::post('kycDetailSearch',[App\Http\Controllers\v1\Operation\KYCController::class,'searchDetails']);
        Route::post('kycExport',[App\Http\Controllers\v1\Operation\KYCController::class,'export']);


        Route::get('showTrans',[App\Http\Controllers\v1\Operation\CommonController::class,'showTrans']);
        // Route::get('checkTransUsingTIN',[App\Http\Controllers\v1\Operation\CommonController::class,'checkTransUsingTIN']);


        Route::get('mfTraxShow',[App\Http\Controllers\v1\Operation\FinancialController::class,'index']);
        Route::get('mfTraxCreateShow',[App\Http\Controllers\v1\Operation\FinancialController::class,'createShow']);
        Route::post('mfTraxCreate',[App\Http\Controllers\v1\Operation\FinancialController::class,'create']);
        Route::post('mfTraxUpdate',[App\Http\Controllers\v1\Operation\FinancialController::class,'update']);
        Route::get('mfTraxDetailSearch',[App\Http\Controllers\v1\Operation\FinancialController::class,'searchDetails']);
        Route::post('mfTraxDetailSearch',[App\Http\Controllers\v1\Operation\FinancialController::class,'searchDetails']);
        Route::post('mfTraxExport',[App\Http\Controllers\v1\Operation\FinancialController::class,'export']);
        
        Route::get('mfTraxFolioDetails',[App\Http\Controllers\v1\Operation\FinancialController::class,'getFolioDetails']);

        Route::get('daysheetReport',[App\Http\Controllers\v1\Operation\ReportController::class,'index']);


        Route::get('ackDetailSearch',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'searchDetails']);
        Route::post('ackDetailSearch',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'searchDetails']);
        Route::post('ackExport',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'export']);
        Route::post('ackUpload',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'update']);
        Route::post('ackFinalSubmit',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'finalSubmit']);

        // =========================== operations routes end =======================================

        // Route::post('index1',[App\Http\Controllers\v1\Master\TestController::class,'index1']);


        // ==========================================Cron routes===========================================
        Route::get('nfoTOongoing',[App\Http\Controllers\v1\Cron\SchemeController::class,'nfoTOongoing']);
    // });
});