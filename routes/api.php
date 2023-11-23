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

// ========================For Mutual Fund================================
Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::get('getip',[App\Http\Controllers\v1\TestController::class,'ShowIp']);
        Route::get('index1',[App\Http\Controllers\v1\TestController::class,'index1']);
        Route::get('index2',[App\Http\Controllers\v1\TestController::class,'index2']);
        Route::get('index3',[App\Http\Controllers\v1\TestController::class,'index3']);

        Route::post('register',[App\Http\Controllers\v1\RegisterController::class,'register']);
        Route::post('login',[App\Http\Controllers\v1\LoginController::class,'login']);
        Route::middleware(['auth:api'])->group(function () {
            Route::post('logout',[App\Http\Controllers\v1\LoginController::class,'logout']);
            Route::get('users',[App\Http\Controllers\v1\HomeController::class,'index']);
            Route::post('changePassword',[App\Http\Controllers\v1\HomeController::class,'chnagePassword']);

            Route::get('mdparams',[App\Http\Controllers\v1\CommonController::class,'CommonParamValue']);

            /* ******************************** Start Common API ****************************   */
            // branch
            Route::get('branch',[App\Http\Controllers\v1\Master\BranchController::class,'index']);
            Route::post('branchAddEdit',[App\Http\Controllers\v1\Master\BranchController::class,'createUpdate']);

            // business type
            Route::get('businessType',[App\Http\Controllers\v1\Master\BusinessTypeController::class,'index']);
            Route::post('businessTypeAddEdit',[App\Http\Controllers\v1\Master\BusinessTypeController::class,'createUpdate']);

            Route::get('employee',[App\Http\Controllers\v1\Master\EmployeeController::class,'index']);
            Route::post('employeeAdd',[App\Http\Controllers\v1\Master\EmployeeController::class,'create']);
            Route::post('employeeEdit',[App\Http\Controllers\v1\Master\EmployeeController::class,'update']);

            Route::get('subbroker',[App\Http\Controllers\v1\Master\SubBrokerController::class,'index']);
            Route::post('subbrokerAddEdit',[App\Http\Controllers\v1\Master\SubBrokerController::class,'createUpdate']);

            /* ******************************** End Common API ****************************   */


            Route::get('rnt',[App\Http\Controllers\v1\Master\RNTController::class,'index']);
            Route::get('rntDetailSearch',[App\Http\Controllers\v1\Master\RNTController::class,'searchDetails']);
            Route::post('rntDetailSearch',[App\Http\Controllers\v1\Master\RNTController::class,'searchDetails']);
            Route::post('rntExport',[App\Http\Controllers\v1\Master\RNTController::class,'export']);
            Route::post('rntAddEdit',[App\Http\Controllers\v1\Master\RNTController::class,'createUpdate']);
            Route::post('rntimport', [App\Http\Controllers\v1\Master\RNTController::class,'import']);
            Route::post('rntDelete', [App\Http\Controllers\v1\Master\RNTController::class,'delete']);

            // Route::get('product',[App\Http\Controllers\v1\Master\ProductController::class,'index']);
            // Route::post('productAddEdit',[App\Http\Controllers\v1\Master\ProductController::class,'createUpdate']);

            Route::get('amc',[App\Http\Controllers\v1\Master\AMCController::class,'index']);
            Route::get('amcDetailSearch',[App\Http\Controllers\v1\Master\AMCController::class,'searchDetails']);
            Route::post('amcDetailSearch',[App\Http\Controllers\v1\Master\AMCController::class,'searchDetails']);
            Route::post('amcExport',[App\Http\Controllers\v1\Master\AMCController::class,'export']);
            Route::post('amcAddEdit',[App\Http\Controllers\v1\Master\AMCController::class,'createUpdate']);
            Route::post('amcimport', [App\Http\Controllers\v1\Master\AMCController::class,'import']);
            Route::post('amcDelete', [App\Http\Controllers\v1\Master\AMCController::class,'delete']);


            Route::post('amcMerge',[App\Http\Controllers\v1\Master\AMCController::class,'merge']);
            Route::post('amcReplace',[App\Http\Controllers\v1\Master\AMCController::class,'replace']);
            Route::post('amcAcquisition',[App\Http\Controllers\v1\Master\AMCController::class,'acquisition']);


            
            Route::get('plan',[App\Http\Controllers\v1\Master\PlanController::class,'index']);
            Route::get('planDetailSearch',[App\Http\Controllers\v1\Master\PlanController::class,'searchDetails']);
            Route::post('planDetailSearch',[App\Http\Controllers\v1\Master\PlanController::class,'searchDetails']);
            Route::post('planExport',[App\Http\Controllers\v1\Master\PlanController::class,'export']);
            Route::post('planAddEdit',[App\Http\Controllers\v1\Master\PlanController::class,'createUpdate']);
            Route::post('planimport', [App\Http\Controllers\v1\Master\PlanController::class,'import']);
            Route::post('planDelete', [App\Http\Controllers\v1\Master\PlanController::class,'delete']);

            Route::get('option',[App\Http\Controllers\v1\Master\OptionController::class,'index']);
            Route::get('optionDetailSearch',[App\Http\Controllers\v1\Master\OptionController::class,'searchDetails']);
            Route::post('optionDetailSearch',[App\Http\Controllers\v1\Master\OptionController::class,'searchDetails']);
            Route::post('optionExport',[App\Http\Controllers\v1\Master\OptionController::class,'export']);
            Route::post('optionAddEdit',[App\Http\Controllers\v1\Master\OptionController::class,'createUpdate']);
            Route::post('optionimport', [App\Http\Controllers\v1\Master\OptionController::class,'import']);
            Route::post('optionDelete', [App\Http\Controllers\v1\Master\OptionController::class,'delete']);

            Route::get('category',[App\Http\Controllers\v1\Master\CategoryController::class,'index']);
            Route::get('categoryDetailSearch',[App\Http\Controllers\v1\Master\CategoryController::class,'searchDetails']);
            Route::post('categoryDetailSearch',[App\Http\Controllers\v1\Master\CategoryController::class,'searchDetails']);
            Route::post('categoryExport',[App\Http\Controllers\v1\Master\CategoryController::class,'export']);
            Route::post('categoryAddEdit',[App\Http\Controllers\v1\Master\CategoryController::class,'createUpdate']);
            Route::post('categoryimport', [App\Http\Controllers\v1\Master\CategoryController::class,'import']);
            Route::post('catDelete', [App\Http\Controllers\v1\Master\CategoryController::class,'delete']);

            Route::get('subcategory',[App\Http\Controllers\v1\Master\SubcategoryController::class,'index']);
            Route::get('subcategoryDetailSearch',[App\Http\Controllers\v1\Master\SubcategoryController::class,'searchDetails']);
            Route::post('subcategoryDetailSearch',[App\Http\Controllers\v1\Master\SubcategoryController::class,'searchDetails']);
            Route::post('subcategoryExport',[App\Http\Controllers\v1\Master\SubcategoryController::class,'export']);
            Route::post('subcategoryAddEdit',[App\Http\Controllers\v1\Master\SubcategoryController::class,'createUpdate']);
            Route::post('subcategoryimport', [App\Http\Controllers\v1\Master\SubcategoryController::class,'import']);
            Route::post('subcatDelete', [App\Http\Controllers\v1\Master\SubcategoryController::class,'delete']);

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
            Route::post('schemeDelete', [App\Http\Controllers\v1\Master\SchemeController::class,'delete']);

            Route::post('schemeMerge',[App\Http\Controllers\v1\Master\SchemeController::class,'merge']);
            Route::post('schemeReplace',[App\Http\Controllers\v1\Master\SchemeController::class,'replace']);
            Route::post('schemeAcquisition',[App\Http\Controllers\v1\Master\SchemeController::class,'acquisition']);

            Route::get('schemeISIN',[App\Http\Controllers\v1\Master\SchemeISINController::class,'index']);
            Route::get('schemeISINDetailSearch',[App\Http\Controllers\v1\Master\SchemeISINController::class,'searchDetails']);
            Route::post('schemeISINDetailSearch',[App\Http\Controllers\v1\Master\SchemeISINController::class,'searchDetails']);
            Route::post('schemeISINExport',[App\Http\Controllers\v1\Master\SchemeISINController::class,'export']);
            Route::post('schemeISINAddEdit',[App\Http\Controllers\v1\Master\SchemeISINController::class,'createUpdate']);
            Route::post('schemeISINimport', [App\Http\Controllers\v1\Master\SchemeISINController::class,'import']);
            Route::post('schemeISINDelete', [App\Http\Controllers\v1\Master\SchemeISINController::class,'delete']);

            Route::get('depositbank',[App\Http\Controllers\v1\Master\DepositBankController::class,'index']);
            Route::get('depositbankDetailSearch',[App\Http\Controllers\v1\Master\DepositBankController::class,'searchDetails']);
            Route::post('depositbankDetailSearch',[App\Http\Controllers\v1\Master\DepositBankController::class,'searchDetails']);
            Route::post('depositbankExport',[App\Http\Controllers\v1\Master\DepositBankController::class,'export']);
            Route::post('depositbankAddEdit',[App\Http\Controllers\v1\Master\DepositBankController::class,'createUpdate']);
            Route::post('depositbankimport', [App\Http\Controllers\v1\Master\DepositBankController::class,'import']);
            Route::post('depositbankDelete', [App\Http\Controllers\v1\Master\DepositBankController::class,'delete']);

            Route::get('documenttype',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'index']);
            Route::get('documenttypeDetailSearch',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'searchDetails']);
            Route::post('documenttypeDetailSearch',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'searchDetails']);
            Route::post('documenttypeExport',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'export']);
            Route::post('documenttypeAddEdit',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'createUpdate']);
            Route::post('documenttypeimport',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'import']);


            Route::get('exchange',[App\Http\Controllers\v1\Master\ExchangeController::class,'index']);
            Route::any('exchangeDetailSearch',[App\Http\Controllers\v1\Master\ExchangeController::class,'searchDetails']);
            Route::post('exchangeExport',[App\Http\Controllers\v1\Master\ExchangeController::class,'export']);
            Route::post('exchangeAddEdit',[App\Http\Controllers\v1\Master\ExchangeController::class,'createUpdate']);
            Route::post('exchangeimport', [App\Http\Controllers\v1\Master\ExchangeController::class,'import']);
            Route::post('exchangeDelete', [App\Http\Controllers\v1\Master\ExchangeController::class,'delete']);

            Route::get('benchmark',[App\Http\Controllers\v1\Master\BenchmarkController::class,'index']);
            Route::any('benchmarkDetailSearch',[App\Http\Controllers\v1\Master\BenchmarkController::class,'searchDetails']);
            Route::post('benchmarkExport',[App\Http\Controllers\v1\Master\BenchmarkController::class,'export']);
            Route::post('benchmarkAddEdit',[App\Http\Controllers\v1\Master\BenchmarkController::class,'createUpdate']);
            Route::post('benchmarkImport', [App\Http\Controllers\v1\Master\BenchmarkController::class,'import']);
            Route::post('benchmarkDelete', [App\Http\Controllers\v1\Master\BenchmarkController::class,'delete']);

            Route::get('benchmarkScheme',[App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'index']);
            Route::any('benchmarkSchemeDetailSearch',[App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'searchDetails']);
            Route::post('benchmarkSchemeExport',[App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'export']);
            Route::post('benchmarkSchemeAddEdit',[App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'createUpdate']);
            Route::post('benchmarkSchemeimport', [App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'import']);
            Route::post('benchmarkSchemeDelete', [App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'delete']);
            
            // =============================geography masters start=================================
            Route::get('country',[App\Http\Controllers\v1\Master\CountryController::class,'index']);
            Route::any('countrySearch',[App\Http\Controllers\v1\Master\CountryController::class,'searchDetails']);
            Route::post('countryExport',[App\Http\Controllers\v1\Master\CountryController::class,'export']);
            Route::post('countryAddEdit',[App\Http\Controllers\v1\Master\CountryController::class,'createUpdate']);
            Route::post('countryimport',[App\Http\Controllers\v1\Master\CountryController::class,'import']);

            Route::get('states',[App\Http\Controllers\v1\Master\StateController::class,'index']);
            Route::any('stateSearch',[App\Http\Controllers\v1\Master\StateController::class,'searchDetails']);
            Route::post('stateExport',[App\Http\Controllers\v1\Master\StateController::class,'export']);
            Route::post('stateAddEdit',[App\Http\Controllers\v1\Master\StateController::class,'createUpdate']);
            Route::post('stateimport',[App\Http\Controllers\v1\Master\StateController::class,'import']);

            Route::get('districts',[App\Http\Controllers\v1\Master\DistrictController::class,'index']);
            Route::any('districtSearch',[App\Http\Controllers\v1\Master\DistrictController::class,'searchDetails']);
            Route::post('districtExport',[App\Http\Controllers\v1\Master\DistrictController::class,'export']);
            Route::post('districtAddEdit',[App\Http\Controllers\v1\Master\DistrictController::class,'createUpdate']);
            Route::post('districtimport',[App\Http\Controllers\v1\Master\DistrictController::class,'import']);

            Route::get('city',[App\Http\Controllers\v1\Master\CityController::class,'index']);
            Route::any('citySearch',[App\Http\Controllers\v1\Master\CityController::class,'searchDetails']);
            Route::post('cityExport',[App\Http\Controllers\v1\Master\CityController::class,'export']);
            Route::post('cityAddEdit',[App\Http\Controllers\v1\Master\CityController::class,'createUpdate']);
            Route::post('cityimport',[App\Http\Controllers\v1\Master\CityController::class,'import']);

            Route::get('pincode',[App\Http\Controllers\v1\Master\PincodeController::class,'index']);
            Route::any('pincodeSearch',[App\Http\Controllers\v1\Master\PincodeController::class,'searchDetails']);
            Route::post('pincodeExport',[App\Http\Controllers\v1\Master\PincodeController::class,'export']);
            Route::post('pincodeAddEdit',[App\Http\Controllers\v1\Master\PincodeController::class,'createUpdate']);
            Route::post('pincodeimport',[App\Http\Controllers\v1\Master\PincodeController::class,'import']);

            Route::get('cityType',[App\Http\Controllers\v1\Master\CityTypeController::class,'index']);
            Route::any('cityTypeSearch',[App\Http\Controllers\v1\Master\CityTypeController::class,'searchDetails']);
            Route::post('cityTypeExport',[App\Http\Controllers\v1\Master\CityTypeController::class,'export']);
            Route::post('cityTypeAddEdit',[App\Http\Controllers\v1\Master\CityTypeController::class,'createUpdate']);
            Route::post('cityTypeimport',[App\Http\Controllers\v1\Master\CityTypeController::class,'import']);

            Route::post('cityTypeMap',[App\Http\Controllers\v1\Master\CityTypeController::class,'map']);

            Route::any('geographyDetailSearch',[App\Http\Controllers\v1\Master\PincodeController::class,'searchDetails']);
            Route::post('geographyExport',[App\Http\Controllers\v1\Master\PincodeController::class,'geographyExport']);

            // =============================geography masters end=================================

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
            Route::post('clientDelete', [App\Http\Controllers\v1\Master\ClientController::class,'delete']);
            
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

            Route::any('kycAckDetailSearch',[App\Http\Controllers\v1\Operation\KYCAckController::class,'searchDetails']);
            Route::post('kycAckExport',[App\Http\Controllers\v1\Operation\KYCAckController::class,'export']);
            Route::post('kycAckUpload',[App\Http\Controllers\v1\Operation\KYCAckController::class,'update']);
            Route::post('kycAckFinalSubmit',[App\Http\Controllers\v1\Operation\KYCAckController::class,'finalSubmit']);

            Route::any('kycManualUpdateDetailSearch',[App\Http\Controllers\v1\Operation\KYCManualUpdateController::class,'searchDetails']);
            Route::post('kycManualUpdateExport',[App\Http\Controllers\v1\Operation\KYCManualUpdateController::class,'export']);
            Route::post('kycManualUpdate',[App\Http\Controllers\v1\Operation\KYCManualUpdateController::class,'update']);
            Route::post('kycManualUpdateFinalSubmit',[App\Http\Controllers\v1\Operation\KYCManualUpdateController::class,'finalSubmit']);


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

            Route::any('manualUpdateDetailSearch',[App\Http\Controllers\v1\Operation\ManualUpdateController::class,'searchDetails']);
            Route::post('manualUpdateExport',[App\Http\Controllers\v1\Operation\ManualUpdateController::class,'export']);
            Route::post('manualUpdate',[App\Http\Controllers\v1\Operation\ManualUpdateController::class,'update']);
            Route::post('manualUpdateFinalSubmit',[App\Http\Controllers\v1\Operation\ManualUpdateController::class,'finalSubmit']);

            // =========================== operations routes end =======================================

            // Route::post('index1',[App\Http\Controllers\v1\Master\TestController::class,'index1']);

            /*  ********************************* Start Common Report *************************** */

            
            /*  ********************************* End Common Report *************************** */


            // ==========================================Cron routes===========================================
            Route::get('nfoTOongoing',[App\Http\Controllers\v1\Cron\SchemeController::class,'nfoTOongoing']);

            
            /*********************************** start Mail Back Process ************************ */

            Route::get('mailbackFileType',[App\Http\Controllers\v1\Master\MailBackController::class,'fileType']);
            Route::get('mailbackFileName',[App\Http\Controllers\v1\Master\MailBackController::class,'fileName']);

            // Route::post('uploadTransDetails',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'upload']);
            Route::post('mailbackProcess',[App\Http\Controllers\v1\Master\MailBackController::class,'upload']);
            Route::any('mailbackProcessDetails',[App\Http\Controllers\v1\Master\MailBackController::class,'Details']);

            /*********************************** start Mail Back Process ************************ */

            /*********************************** start Mail Back Mismatch ************************ */
            Route::any('mailbackMismatch',[App\Http\Controllers\v1\Master\MailBackController::class,'misMatch']);
            Route::any('mailbackMismatchNAV',[App\Http\Controllers\v1\Master\MailBackController::class,'misMatchNAV']);  // NAV mismatch details
            Route::any('mailbackMismatchSipStp',[App\Http\Controllers\v1\Master\MailBackController::class,'misMatchSipStp']);  // NAV mismatch details
            Route::any('mailbackMismatchFolio',[App\Http\Controllers\v1\Master\MailBackController::class,'misMatchFolio']);  // NAV mismatch details
        
            Route::post('mailbackMismatchLock',[App\Http\Controllers\v1\Master\MailBackController::class,'lockTransaction']);
            Route::post('mailbackMismatchUnlock',[App\Http\Controllers\v1\Master\MailBackController::class,'unlockTransaction']);
            
            Route::any('mailbackMismatchAll',[App\Http\Controllers\v1\Master\MailBackController::class,'allMismatch']);  // all mismatch details

            /*********************************** End Mail Back Mismatch ************************ */

            /*************************************************Start For file help************************************************/
            Route::any('rntTransTypeSubtype',[App\Http\Controllers\v1\Master\MFTransTypeSubTypeController::class,'Details']);
            Route::any('rntTransTypeSubtypeShow',[App\Http\Controllers\v1\Master\MFTransTypeSubTypeController::class,'index']);
            Route::post('rntTransTypeSubtypeAddEdit',[App\Http\Controllers\v1\Master\MFTransTypeSubTypeController::class,'CreateUpdate']);

            Route::any('rntSystematicFrequency',[App\Http\Controllers\v1\Master\SystematicFrequencyController::class,'Details']);
            Route::any('rntSystematicFrequencyShow',[App\Http\Controllers\v1\Master\SystematicFrequencyController::class,'index']);
            Route::post('rntSystematicFrequencyAddEdit',[App\Http\Controllers\v1\Master\SystematicFrequencyController::class,'CreateUpdate']);

            Route::any('rntSystematicTransType',[App\Http\Controllers\v1\Master\SystematicTransTypeController::class,'Details']);
            Route::any('rntSystematicTransTypeShow',[App\Http\Controllers\v1\Master\SystematicTransTypeController::class,'index']);
            Route::post('rntSystematicTransTypeAddEdit',[App\Http\Controllers\v1\Master\SystematicTransTypeController::class,'CreateUpdate']);

            Route::any('rntSystematicUnregister',[App\Http\Controllers\v1\Master\SystematicUnregisterController::class,'Details']);
            Route::any('rntSystematicUnregisterShow',[App\Http\Controllers\v1\Master\SystematicUnregisterController::class,'index']);
            Route::post('rntSystematicUnregisterAddEdit',[App\Http\Controllers\v1\Master\SystematicUnregisterController::class,'CreateUpdate']);

            Route::any('rntFolioDetails',[App\Http\Controllers\v1\Master\FolioTaxStatusController::class,'Details']);
            Route::any('rntFolioDetailsShow',[App\Http\Controllers\v1\Master\FolioTaxStatusController::class,'index']);
            Route::post('rntFolioDetailsAddEdit',[App\Http\Controllers\v1\Master\FolioTaxStatusController::class,'CreateUpdate']);

            Route::any('fileUploadHelp',[App\Http\Controllers\v1\Master\UploadFileHelpController::class,'index']);
            Route::post('fileUploadHelpAddEdit',[App\Http\Controllers\v1\Master\UploadFileHelpController::class,'CreateUpdate']);

            /*************************************************End For file help************************************************/

            /*************************************************Start For Report************************************************/
            Route::any('searchClient',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'searchClient']);
            
            Route::any('showTransDetails',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'search']); // Search Transaction details
            Route::any('showNAVDetails',[App\Http\Controllers\v1\Operation\NAVDetailsController::class,'search']);  // Search NAV details
            Route::any('showSipStpDetails',[App\Http\Controllers\v1\Operation\SipStpTransController::class,'search']);  // Search sip stp details
            Route::any('showFolioDetails',[App\Http\Controllers\v1\Operation\FolioDetailsController::class,'search']);  // Search sip stp details

            /*************************************************End For Report************************************************/

            /*************************************************Start Delete Report************************************************/

            Route::any('showDeleteTransDetails',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'searchDelete']);
            Route::post('DeleteTransDetails',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'delete']);
            Route::any('unlockTransDetails',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'unlock']);
            
            /*************************************************End Delete Report************************************************/

            /*************************************************Start TAB sub TAB Show api************************************************/
            Route::any('showTab1',[App\Http\Controllers\v1\TabController::class,'Tab1']);  // Search sip stp details

            /*************************************************End TAB sub TAB Show api************************************************/

            Route::prefix('client')->group(function () {

            });
        });

    // });
});