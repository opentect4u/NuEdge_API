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

// ========================For Mutual Fund related routes================================
Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::get('getip',[App\Http\Controllers\v1\TestController::class,'ShowIp']); // show ip address of the request
        // Route::get('index1',[App\Http\Controllers\v1\TestController::class,'index1']);
        // Route::get('index2',[App\Http\Controllers\v1\TestController::class,'index2']);
        // Route::get('index3',[App\Http\Controllers\v1\TestController::class,'index3']);
        // Route::get('hash',[App\Http\Controllers\TestController::class,'hash']);

        // Route::post('register',[App\Http\Controllers\v1\RegisterController::class,'register']);
        Route::post('login',[App\Http\Controllers\v1\LoginController::class,'login']); // login user
        Route::middleware(['auth:api'])->group(function () {
            Route::post('logout',[App\Http\Controllers\v1\LoginController::class,'logout']); // logout user
            Route::get('users',[App\Http\Controllers\v1\HomeController::class,'index']); // show a particular users
            Route::post('changePassword',[App\Http\Controllers\v1\HomeController::class,'chnagePassword']); // change password of the user

            Route::get('mdparams',[App\Http\Controllers\v1\CommonController::class,'CommonParamValue']); // get common parameter value

            /* ******************************** Start Common API ****************************   */
            // branch
            Route::get('branch',[App\Http\Controllers\v1\Master\BranchController::class,'index']); // show branch details also use for dropdown list
            Route::post('branchAddEdit',[App\Http\Controllers\v1\Master\BranchController::class,'createUpdate']); // create and update branch details

            // business type
            Route::get('businessType',[App\Http\Controllers\v1\Master\BusinessTypeController::class,'index']); // show business type details also use for dropdown list
            Route::post('businessTypeAddEdit',[App\Http\Controllers\v1\Master\BusinessTypeController::class,'createUpdate']); // create and update business type details

            Route::get('employee',[App\Http\Controllers\v1\Master\EmployeeController::class,'index']); // show employee details also use for dropdown list
            Route::post('employeeAdd',[App\Http\Controllers\v1\Master\EmployeeController::class,'create']); // create employee details
            Route::post('employeeEdit',[App\Http\Controllers\v1\Master\EmployeeController::class,'update']); // update employee details

            Route::get('subbroker',[App\Http\Controllers\v1\Master\SubBrokerController::class,'index']); // show subbroker details also use for dropdown list
            Route::post('subbrokerAddEdit',[App\Http\Controllers\v1\Master\SubBrokerController::class,'createUpdate']); // create and update subbroker details

            /* ******************************** End Common API ****************************   */


            Route::get('rnt',[App\Http\Controllers\v1\Master\RNTController::class,'index']); // show RNT details also use for dropdown list
            Route::get('rntDetailSearch',[App\Http\Controllers\v1\Master\RNTController::class,'searchDetails']); // search RNT by name or code
            Route::post('rntDetailSearch',[App\Http\Controllers\v1\Master\RNTController::class,'searchDetails']); // search RNT by name or code
            Route::post('rntExport',[App\Http\Controllers\v1\Master\RNTController::class,'export']); // export RNT details
            Route::post('rntAddEdit',[App\Http\Controllers\v1\Master\RNTController::class,'createUpdate']);     // create and update RNT details
            Route::post('rntimport', [App\Http\Controllers\v1\Master\RNTController::class,'import']); // import RNT details
            Route::post('rntDelete', [App\Http\Controllers\v1\Master\RNTController::class,'delete']); // delete RNT details

            Route::get('product',[App\Http\Controllers\v1\Master\ProductController::class,'index']); // show product details also use for dropdown list
            Route::post('productAddEdit',[App\Http\Controllers\v1\Master\ProductController::class,'createUpdate']); // create and update product details

            Route::get('amc',[App\Http\Controllers\v1\Master\AMCController::class,'index']); // show AMC details also use for dropdown list
            Route::get('amcDetailSearch',[App\Http\Controllers\v1\Master\AMCController::class,'searchDetails']); // search AMC by name or code
            Route::post('amcDetailSearch',[App\Http\Controllers\v1\Master\AMCController::class,'searchDetails']); // search AMC by name or code
            Route::post('amcExport',[App\Http\Controllers\v1\Master\AMCController::class,'export']); // export AMC details
            Route::post('amcAddEdit',[App\Http\Controllers\v1\Master\AMCController::class,'createUpdate']);   // create and update AMC details
            Route::post('amcimport', [App\Http\Controllers\v1\Master\AMCController::class,'import']); // import AMC details
            Route::post('amcDelete', [App\Http\Controllers\v1\Master\AMCController::class,'delete']); // delete AMC details


            Route::post('amcMerge',[App\Http\Controllers\v1\Master\AMCController::class,'merge']); // merge AMC details
            Route::post('amcReplace',[App\Http\Controllers\v1\Master\AMCController::class,'replace']); // replace AMC details
            Route::post('amcAcquisition',[App\Http\Controllers\v1\Master\AMCController::class,'acquisition']); // acquisition AMC details


            
            Route::get('plan',[App\Http\Controllers\v1\Master\PlanController::class,'index']); // show plan details also use for dropdown list
            Route::get('planDetailSearch',[App\Http\Controllers\v1\Master\PlanController::class,'searchDetails']); // search plan by name or code
            Route::post('planDetailSearch',[App\Http\Controllers\v1\Master\PlanController::class,'searchDetails']); // search plan by name or code
            Route::post('planExport',[App\Http\Controllers\v1\Master\PlanController::class,'export']); // export plan details
            Route::post('planAddEdit',[App\Http\Controllers\v1\Master\PlanController::class,'createUpdate']); // create and update plan details
            Route::post('planimport', [App\Http\Controllers\v1\Master\PlanController::class,'import']); // import plan details
            Route::post('planDelete', [App\Http\Controllers\v1\Master\PlanController::class,'delete']); // delete plan details

            Route::get('option',[App\Http\Controllers\v1\Master\OptionController::class,'index']); // show option details also use for dropdown list
            Route::get('optionDetailSearch',[App\Http\Controllers\v1\Master\OptionController::class,'searchDetails']); // search option by name or code
            Route::post('optionDetailSearch',[App\Http\Controllers\v1\Master\OptionController::class,'searchDetails']); // search option by name or code
            Route::post('optionExport',[App\Http\Controllers\v1\Master\OptionController::class,'export']); // export option details
            Route::post('optionAddEdit',[App\Http\Controllers\v1\Master\OptionController::class,'createUpdate']); // create and update option details
            Route::post('optionimport', [App\Http\Controllers\v1\Master\OptionController::class,'import']); // import option details
            Route::post('optionDelete', [App\Http\Controllers\v1\Master\OptionController::class,'delete']); // delete option details

            Route::get('category',[App\Http\Controllers\v1\Master\CategoryController::class,'index']); // show category details also use for dropdown list
            Route::get('categoryDetailSearch',[App\Http\Controllers\v1\Master\CategoryController::class,'searchDetails']); // search category by name or code
            Route::post('categoryDetailSearch',[App\Http\Controllers\v1\Master\CategoryController::class,'searchDetails']); // search category by name or code
            Route::post('categoryExport',[App\Http\Controllers\v1\Master\CategoryController::class,'export']); // export category details
            Route::post('categoryAddEdit',[App\Http\Controllers\v1\Master\CategoryController::class,'createUpdate']); // create and update category details
            Route::post('categoryimport', [App\Http\Controllers\v1\Master\CategoryController::class,'import']); // import category details
            Route::post('catDelete', [App\Http\Controllers\v1\Master\CategoryController::class,'delete']); // delete category details

            Route::get('subcategory',[App\Http\Controllers\v1\Master\SubcategoryController::class,'index']); // show subcategory details also use for dropdown list
            Route::get('subcategoryDetailSearch',[App\Http\Controllers\v1\Master\SubcategoryController::class,'searchDetails']);    // search subcategory by name or code
            Route::post('subcategoryDetailSearch',[App\Http\Controllers\v1\Master\SubcategoryController::class,'searchDetails']); // search subcategory by name or code
            Route::post('subcategoryExport',[App\Http\Controllers\v1\Master\SubcategoryController::class,'export']); // export subcategory details
            Route::post('subcategoryAddEdit',[App\Http\Controllers\v1\Master\SubcategoryController::class,'createUpdate']); // create and update subcategory details
            Route::post('subcategoryimport', [App\Http\Controllers\v1\Master\SubcategoryController::class,'import']); // import subcategory details
            Route::post('subcatDelete', [App\Http\Controllers\v1\Master\SubcategoryController::class,'delete']); // delete subcategory details

            Route::get('formtype',[App\Http\Controllers\v1\Master\FormTypeController::class,'index']); // show form type details also use for dropdown list
            Route::post('formtypeAddEdit',[App\Http\Controllers\v1\Master\FormTypeController::class,'createUpdate']); // create and update form type details

            Route::get('transctiontype',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'index']); // show transaction type details also use for dropdown list
            Route::get('transctiontypeSearch',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'searchDetails']);    // search transaction type by name or code
            Route::post('transctiontypeSearch',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'searchDetails']); // search transaction type by name or code
            Route::post('transctiontypeExport',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'export']); // export transaction type details
            Route::post('transctiontypeAddEdit',[App\Http\Controllers\v1\Master\TransctionTypeController::class,'createUpdate']); // create and update transaction type details

            Route::get('transction',[App\Http\Controllers\v1\Master\TransctionController::class,'index']); // show transaction details also use for dropdown list
            Route::get('transctionSearch',[App\Http\Controllers\v1\Master\TransctionController::class,'searchDetails']); // search transaction by name or code
            Route::post('transctionSearch',[App\Http\Controllers\v1\Master\TransctionController::class,'searchDetails']); // search transaction by name or code
            Route::post('transctionExport',[App\Http\Controllers\v1\Master\TransctionController::class,'export']); // export transaction details
            Route::post('transctionAddEdit',[App\Http\Controllers\v1\Master\TransctionController::class,'createUpdate']); // create and update transaction details

            Route::get('sipType',[App\Http\Controllers\v1\Master\SIPTypeController::class,'index']); // show SIP type details also use for dropdown list
            Route::get('sipTypeSearch',[App\Http\Controllers\v1\Master\SIPTypeController::class,'searchDetails']); // search SIP type by name or code
            Route::post('sipTypeSearch',[App\Http\Controllers\v1\Master\SIPTypeController::class,'searchDetails']); // search SIP type by name or code
            Route::post('sipTypeExport',[App\Http\Controllers\v1\Master\SIPTypeController::class,'export']); // export SIP type details
            Route::post('sipTypeAddEdit',[App\Http\Controllers\v1\Master\SIPTypeController::class,'createUpdate']); // create and update SIP type details

            Route::get('scheme',[App\Http\Controllers\v1\Master\SchemeController::class,'index']); // show scheme details also use for dropdown list
            Route::get('schemeDetailSearch',[App\Http\Controllers\v1\Master\SchemeController::class,'searchDetails']); // search scheme by name or code
            Route::post('schemeDetailSearch',[App\Http\Controllers\v1\Master\SchemeController::class,'searchDetails']); // search scheme by name or code
            Route::post('schemeExport',[App\Http\Controllers\v1\Master\SchemeController::class,'export']); // export scheme details
            Route::post('schemeAddEdit',[App\Http\Controllers\v1\Master\SchemeController::class,'createUpdate']); // create and update scheme details
            Route::post('schemeimport', [App\Http\Controllers\v1\Master\SchemeController::class,'import']); // import scheme details
            Route::post('schemeDelete', [App\Http\Controllers\v1\Master\SchemeController::class,'delete']); // delete scheme details

            Route::post('schemeMerge',[App\Http\Controllers\v1\Master\SchemeController::class,'merge']); // merge scheme details
            Route::post('schemeReplace',[App\Http\Controllers\v1\Master\SchemeController::class,'replace']); // replace scheme details
            Route::post('schemeAcquisition',[App\Http\Controllers\v1\Master\SchemeController::class,'acquisition']); // acquisition scheme details

            Route::get('schemeISIN',[App\Http\Controllers\v1\Master\SchemeISINController::class,'index']); // show scheme ISIN details also use for dropdown list
            Route::get('schemeISINDetailSearch',[App\Http\Controllers\v1\Master\SchemeISINController::class,'searchDetails']); // search scheme ISIN by name or code
            Route::post('schemeISINDetailSearch',[App\Http\Controllers\v1\Master\SchemeISINController::class,'searchDetails']); // search scheme ISIN by name or code
            Route::post('schemeISINExport',[App\Http\Controllers\v1\Master\SchemeISINController::class,'export']); // export scheme ISIN details
            Route::post('schemeISINAddEdit',[App\Http\Controllers\v1\Master\SchemeISINController::class,'createUpdate']); // create and update scheme ISIN details
            Route::post('schemeISINimport', [App\Http\Controllers\v1\Master\SchemeISINController::class,'import']); // import scheme ISIN details
            Route::post('schemeISINDelete', [App\Http\Controllers\v1\Master\SchemeISINController::class,'delete']); // delete scheme ISIN details

            Route::get('depositbank',[App\Http\Controllers\v1\Master\DepositBankController::class,'index']); // show deposit bank details also use for dropdown list
            Route::get('depositbankDetailSearch',[App\Http\Controllers\v1\Master\DepositBankController::class,'searchDetails']); // search deposit bank by name or code
            Route::post('depositbankDetailSearch',[App\Http\Controllers\v1\Master\DepositBankController::class,'searchDetails']); // search deposit bank by name or code
            Route::post('depositbankExport',[App\Http\Controllers\v1\Master\DepositBankController::class,'export']); // export deposit bank details
            Route::post('depositbankAddEdit',[App\Http\Controllers\v1\Master\DepositBankController::class,'createUpdate']); // create and update deposit bank details
            Route::post('depositbankimport', [App\Http\Controllers\v1\Master\DepositBankController::class,'import']); // import deposit bank details
            Route::post('depositbankDelete', [App\Http\Controllers\v1\Master\DepositBankController::class,'delete']); // delete deposit bank details

            Route::get('documenttype',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'index']); // show document type details also use for dropdown list
            Route::get('documenttypeDetailSearch',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'searchDetails']); // search document type by name or code
            Route::post('documenttypeDetailSearch',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'searchDetails']); // search document type by name or code
            Route::post('documenttypeExport',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'export']); // export document type details
            Route::post('documenttypeAddEdit',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'createUpdate']); // create and update document type details
            Route::post('documenttypeimport',[App\Http\Controllers\v1\Master\DocumentTypeController::class,'import']); // import document type details


            Route::get('exchange',[App\Http\Controllers\v1\Master\ExchangeController::class,'index']); // show exchange details also use for dropdown list
            Route::any('exchangeDetailSearch',[App\Http\Controllers\v1\Master\ExchangeController::class,'searchDetails']); // search exchange by name or code
            Route::post('exchangeExport',[App\Http\Controllers\v1\Master\ExchangeController::class,'export']); // export exchange details
            Route::post('exchangeAddEdit',[App\Http\Controllers\v1\Master\ExchangeController::class,'createUpdate']); // create and update exchange details
            Route::post('exchangeimport', [App\Http\Controllers\v1\Master\ExchangeController::class,'import']); // import exchange details
            Route::post('exchangeDelete', [App\Http\Controllers\v1\Master\ExchangeController::class,'delete']); // delete exchange details

            Route::get('benchmark',[App\Http\Controllers\v1\Master\BenchmarkController::class,'index']); // show benchmark details also use for dropdown list
            Route::any('benchmarkDetailSearch',[App\Http\Controllers\v1\Master\BenchmarkController::class,'searchDetails']); // search benchmark by name or code
            Route::post('benchmarkExport',[App\Http\Controllers\v1\Master\BenchmarkController::class,'export']); // export benchmark details
            Route::post('benchmarkAddEdit',[App\Http\Controllers\v1\Master\BenchmarkController::class,'createUpdate']); // create and update benchmark details
            Route::post('benchmarkImport', [App\Http\Controllers\v1\Master\BenchmarkController::class,'import']); // import benchmark details
            Route::post('benchmarkDelete', [App\Http\Controllers\v1\Master\BenchmarkController::class,'delete']); // delete benchmark details

            Route::get('benchmarkScheme',[App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'index']); // show benchmark scheme details also use for dropdown list
            Route::any('benchmarkSchemeDetailSearch',[App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'searchDetails']); // search benchmark scheme by name or code
            Route::post('benchmarkSchemeExport',[App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'export']); // export benchmark scheme details
            Route::post('benchmarkSchemeAddEdit',[App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'createUpdate']); // create and update benchmark scheme details
            Route::post('benchmarkSchemeimport', [App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'import']); // import benchmark scheme details
            Route::post('benchmarkSchemeDelete', [App\Http\Controllers\v1\Operation\BenchmarkSchemeController::class,'delete']); // delete benchmark scheme details
            
            // =============================geography masters start=================================
            Route::get('country',[App\Http\Controllers\v1\Master\CountryController::class,'index']); // show country details also use for dropdown list
            Route::any('countrySearch',[App\Http\Controllers\v1\Master\CountryController::class,'searchDetails']); // search country by name or code
            Route::post('countryExport',[App\Http\Controllers\v1\Master\CountryController::class,'export']); // export country details
            Route::post('countryAddEdit',[App\Http\Controllers\v1\Master\CountryController::class,'createUpdate']); // create and update country details
            Route::post('countryimport',[App\Http\Controllers\v1\Master\CountryController::class,'import']); 

            Route::get('states',[App\Http\Controllers\v1\Master\StateController::class,'index']); // show state details also use for dropdown list
            Route::any('stateSearch',[App\Http\Controllers\v1\Master\StateController::class,'searchDetails']); // search state by name or code
            Route::post('stateExport',[App\Http\Controllers\v1\Master\StateController::class,'export']);    // export state details
            Route::post('stateAddEdit',[App\Http\Controllers\v1\Master\StateController::class,'createUpdate']); // create and update state details
            Route::post('stateimport',[App\Http\Controllers\v1\Master\StateController::class,'import']); // import state details

            Route::get('districts',[App\Http\Controllers\v1\Master\DistrictController::class,'index']); // show district details also use for dropdown list
            Route::any('districtSearch',[App\Http\Controllers\v1\Master\DistrictController::class,'searchDetails']); // search district by name or code
            Route::post('districtExport',[App\Http\Controllers\v1\Master\DistrictController::class,'export']); // export district details
            Route::post('districtAddEdit',[App\Http\Controllers\v1\Master\DistrictController::class,'createUpdate']); // create and update district details
            Route::post('districtimport',[App\Http\Controllers\v1\Master\DistrictController::class,'import']); // import district details

            Route::get('city',[App\Http\Controllers\v1\Master\CityController::class,'index']); // show city details also use for dropdown list
            Route::any('citySearch',[App\Http\Controllers\v1\Master\CityController::class,'searchDetails']); // search city by name or code
            Route::post('cityExport',[App\Http\Controllers\v1\Master\CityController::class,'export']); // export city details
            Route::post('cityAddEdit',[App\Http\Controllers\v1\Master\CityController::class,'createUpdate']); // create and update city details
            Route::post('cityimport',[App\Http\Controllers\v1\Master\CityController::class,'import']); // import city details

            Route::get('pincode',[App\Http\Controllers\v1\Master\PincodeController::class,'index']); // show pincode details also use for dropdown list
            Route::any('pincodeSearch',[App\Http\Controllers\v1\Master\PincodeController::class,'searchDetails']); // search pincode by name or code
            Route::post('pincodeExport',[App\Http\Controllers\v1\Master\PincodeController::class,'export']); // export pincode details
            Route::post('pincodeAddEdit',[App\Http\Controllers\v1\Master\PincodeController::class,'createUpdate']); // create and update pincode details
            Route::post('pincodeimport',[App\Http\Controllers\v1\Master\PincodeController::class,'import']); // import pincode details

            Route::get('cityType',[App\Http\Controllers\v1\Master\CityTypeController::class,'index']); // show city type details also use for dropdown list
            Route::any('cityTypeSearch',[App\Http\Controllers\v1\Master\CityTypeController::class,'searchDetails']); // search city type by name or code
            Route::post('cityTypeExport',[App\Http\Controllers\v1\Master\CityTypeController::class,'export']); // export city type details
            Route::post('cityTypeAddEdit',[App\Http\Controllers\v1\Master\CityTypeController::class,'createUpdate']); // create and update city type details
            Route::post('cityTypeimport',[App\Http\Controllers\v1\Master\CityTypeController::class,'import']); // import city type details

            Route::post('cityTypeMap',[App\Http\Controllers\v1\Master\CityTypeController::class,'map']); // map city type details

            Route::any('geographyDetailSearch',[App\Http\Controllers\v1\Master\PincodeController::class,'searchDetails']); // search geography details by name or code
            Route::post('geographyExport',[App\Http\Controllers\v1\Master\PincodeController::class,'geographyExport']); // export geography details

            Route::get('disclaimer',[App\Http\Controllers\v1\Master\DisclaimerController::class,'index']); // show disclaimer details also use for dropdown list
            Route::post('disclaimerAddEdit',[App\Http\Controllers\v1\Master\DisclaimerController::class,'createUpdate']); // create and update disclaimer details


            // =============================geography masters end=================================

            Route::get('swpType',[App\Http\Controllers\v1\Master\SWPTypeController::class,'index']); // show SWP type details also use for dropdown list
            Route::get('swpTypeSearch',[App\Http\Controllers\v1\Master\SWPTypeController::class,'searchDetails']); // search SWP type by name or code
            Route::post('swpTypeSearch',[App\Http\Controllers\v1\Master\SWPTypeController::class,'searchDetails']); // search SWP type by name or code
            Route::post('swpTypeExport',[App\Http\Controllers\v1\Master\SWPTypeController::class,'export']); // export SWP type details
            Route::post('swpTypeAddEdit',[App\Http\Controllers\v1\Master\SWPTypeController::class,'createUpdate']); // create and update SWP type details

            Route::get('stpType',[App\Http\Controllers\v1\Master\STPTypeController::class,'index']); // show STP type details also use for dropdown list
            Route::get('stpTypeSearch',[App\Http\Controllers\v1\Master\STPTypeController::class,'searchDetails']); // search STP type by name or code
            Route::post('stpTypeSearch',[App\Http\Controllers\v1\Master\STPTypeController::class,'searchDetails']); // search STP type by name or code
            Route::post('stpTypeExport',[App\Http\Controllers\v1\Master\STPTypeController::class,'export']);  // export STP type details
            Route::post('stpTypeAddEdit',[App\Http\Controllers\v1\Master\STPTypeController::class,'createUpdate']); // create and update STP type details

            Route::get('clientType',[App\Http\Controllers\v1\Master\ClientTypeController::class,'index']); // show client type details also use for dropdown list
            Route::get('clientTypeSearch',[App\Http\Controllers\v1\Master\ClientTypeController::class,'searchDetails']); // search client type by name or code
            Route::post('clientTypeSearch',[App\Http\Controllers\v1\Master\ClientTypeController::class,'searchDetails']); // search client type by name or code
            Route::post('clientTypeExport',[App\Http\Controllers\v1\Master\ClientTypeController::class,'export']); // export client type details
            Route::post('clientTypeAddEdit',[App\Http\Controllers\v1\Master\ClientTypeController::class,'createUpdate']); // create and update client type details

            Route::get('email',[App\Http\Controllers\v1\Master\EmailController::class,'index']); // show email details also use for dropdown list
            Route::get('emailSearch',[App\Http\Controllers\v1\Master\EmailController::class,'searchDetails']); // search email by name or code
            Route::post('emailSearch',[App\Http\Controllers\v1\Master\EmailController::class,'searchDetails']); // search email by name or code
            Route::post('emailAddEdit',[App\Http\Controllers\v1\Master\EmailController::class,'createUpdate']); // create and update email details
            Route::post('emailExport',[App\Http\Controllers\v1\Master\EmailController::class,'export']); // export email details
            Route::post('emailAddEdit',[App\Http\Controllers\v1\Master\EmailController::class,'createUpdate']); // create and update email details

            Route::get('taxImplication',[App\Http\Controllers\v1\Master\TaxImplicationController::class,'index']); // show tax implication details also use for dropdown list

            // dropdown onchange routes start
            Route::get('amcUsingPro',[App\Http\Controllers\v1\Master\CommonController::class,'showAMC']); // show AMC based on product
            Route::get('catUsingPro',[App\Http\Controllers\v1\Master\CommonController::class,'showCategory']); // show category based on product
            Route::get('subcatUsingPro',[App\Http\Controllers\v1\Master\CommonController::class,'showSubCategory']); // show subcategory based on product
            // dropdown onchange routes end
            
            // Route::get('depositbank',[App\Http\Controllers\v1\Master\DepositBankController::class,'index']);
            // Route::post('depositbankAddEdit',[App\Http\Controllers\v1\Master\DepositBankController::class,'createUpdate']);

            // ================ operations routes start =====================

            Route::get('formreceived',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'index']); // show form received details also use for dropdown list
            Route::get('formreceivedshow',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'createShow']); // show form received create page
            Route::post('formreceivedAdd',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'create']);  // create form received details
            Route::post('formreceivedEdit',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'update']); // update form received details
            Route::post('formreceivedDelete',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'delete']); // delete form received details
            Route::get('formreceivedDetailSearch',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'searchDetails']); // search form received by name or code
            Route::post('formreceivedDetailSearch',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'searchDetails']); // search form received by name or code
            Route::post('formreceivedExport',[App\Http\Controllers\v1\Operation\FormReceivedController::class,'export']); // export form received details

            Route::get('formtypeUsingPro',[App\Http\Controllers\v1\Operation\CommonController::class,'showFormType']); // show form type based on product
            Route::get('showsubbroker',[App\Http\Controllers\v1\Operation\CommonController::class,'ShowSubBroker']); // show sub broker based on product
            Route::get('subbrocodeUsingarn',[App\Http\Controllers\v1\Operation\CommonController::class,'showSubBrokerCode']); // show sub broker code based on ARN
            Route::get('showTransInFormRec',[App\Http\Controllers\v1\Operation\CommonController::class,'showTransInFormRec']); // show transaction in form received based on ARN

            Route::get('searchWithClient',[App\Http\Controllers\v1\Master\ClientController::class,'searchWithClient']); // search client by name or code
            Route::get('searchWithClientMem',[App\Http\Controllers\v1\Master\ClientController::class,'searchWithClientMem']); // search client member by name or code
            Route::get('searchClientWithoutFamily',[App\Http\Controllers\v1\Master\ClientController::class,'clientWithoutFamily']); // search client without family by name or code
            Route::get('client',[App\Http\Controllers\v1\Master\ClientController::class,'index']); // show client details also use for dropdown list
            Route::post('clientAddEdit',[App\Http\Controllers\v1\Master\ClientController::class,'createUpdate']); // create and update client details
            Route::post('clientimport',[App\Http\Controllers\v1\Master\ClientController::class,'import']); // import client details
            Route::any('clientDetailSearch',[App\Http\Controllers\v1\Master\ClientController::class,'searchDetails']); // search client by name or code
            Route::post('clientExport',[App\Http\Controllers\v1\Master\ClientController::class,'export']); // export client details
            Route::post('clientDelete', [App\Http\Controllers\v1\Master\ClientController::class,'delete']); // delete client details
            Route::get('mergeClient',[App\Http\Controllers\v1\Master\ClientController::class,'searchMergeClient']); // search client for merge by name or code
            
            Route::get('documentsearch',[App\Http\Controllers\v1\Master\DocumentController::class,'search']); // search document by name or code
            Route::get('document',[App\Http\Controllers\v1\Master\DocumentController::class,'index']); // show document details also use for dropdown list
            Route::get('documentshowEdit',[App\Http\Controllers\v1\Master\DocumentController::class,'Edit']); // show document edit page
            Route::post('documentAdd',[App\Http\Controllers\v1\Master\DocumentController::class,'create']); // create document details
            Route::post('documentEdit',[App\Http\Controllers\v1\Master\DocumentController::class,'update']); // update document details
            Route::post('documentimport',[App\Http\Controllers\v1\Master\DocumentController::class,'import']); // import document details

            Route::get('clientFamilyDetailSearch',[App\Http\Controllers\v1\Master\ClientFamilyController::class,'searchDetails']); // search client family by name or code
            Route::get('clientFamilyDetail',[App\Http\Controllers\v1\Master\ClientFamilyController::class,'familyDetail']); // get client family details
            Route::post('clientFamilyAddEdit',[App\Http\Controllers\v1\Master\ClientFamilyController::class,'createUpdate']); // create and update client family details
            Route::post('updateFamilymembers',[App\Http\Controllers\v1\Master\ClientFamilyController::class,'update']); // update client family members
            Route::post('familyDelete',[App\Http\Controllers\v1\Master\ClientFamilyController::class,'delete']); // delete client family details
            Route::get('nonFamilylist',[App\Http\Controllers\v1\Master\ClientFamilyController::class,'nonFamilylist']); // get non family members list


            Route::get('kyc',[App\Http\Controllers\v1\Operation\KYCController::class,'index']); // show KYC details also use for dropdown list
            Route::get('kycshowadd',[App\Http\Controllers\v1\Operation\KYCController::class,'showAdd']); // show KYC create page
            Route::post('kycAddEdit',[App\Http\Controllers\v1\Operation\KYCController::class,'createUpdate']); // create and update KYC details
            Route::get('kycDetailSearch',[App\Http\Controllers\v1\Operation\KYCController::class,'searchDetails']); // search KYC by name or code
            Route::post('kycDetailSearch',[App\Http\Controllers\v1\Operation\KYCController::class,'searchDetails']); // search KYC by name or code
            Route::post('kycExport',[App\Http\Controllers\v1\Operation\KYCController::class,'export']); // export KYC details

            Route::any('kycAckDetailSearch',[App\Http\Controllers\v1\Operation\KYCAckController::class,'searchDetails']); // search KYC acknowledgement by name or code
            Route::post('kycAckExport',[App\Http\Controllers\v1\Operation\KYCAckController::class,'export']); // export KYC acknowledgement details
            Route::post('kycAckUpload',[App\Http\Controllers\v1\Operation\KYCAckController::class,'update']); // update KYC acknowledgement details
            Route::post('kycAckFinalSubmit',[App\Http\Controllers\v1\Operation\KYCAckController::class,'finalSubmit']); // final submit KYC acknowledgement details

            Route::any('kycManualUpdateDetailSearch',[App\Http\Controllers\v1\Operation\KYCManualUpdateController::class,'searchDetails']); // search KYC manual update by name or code
            Route::post('kycManualUpdateExport',[App\Http\Controllers\v1\Operation\KYCManualUpdateController::class,'export']); // export KYC manual update details
            Route::post('kycManualUpdate',[App\Http\Controllers\v1\Operation\KYCManualUpdateController::class,'update']); // update KYC manual update details
            Route::post('kycManualUpdateFinalSubmit',[App\Http\Controllers\v1\Operation\KYCManualUpdateController::class,'finalSubmit']); // final submit KYC manual update details


            Route::get('showTrans',[App\Http\Controllers\v1\Operation\CommonController::class,'showTrans']); // show transaction details based on ARN
            // Route::get('checkTransUsingTIN',[App\Http\Controllers\v1\Operation\CommonController::class,'checkTransUsingTIN']);


            Route::get('kycStatus',[App\Http\Controllers\v1\Operation\FinancialController::class,'getKYCStatus']); // get KYC status based on client code
            Route::get('mfTraxShow',[App\Http\Controllers\v1\Operation\FinancialController::class,'index']); // show mutual fund transaction details also use for dropdown list
            Route::get('mfTraxCreateShow',[App\Http\Controllers\v1\Operation\FinancialController::class,'createShow']); // show mutual fund transaction create page
            Route::post('mfTraxCreate',[App\Http\Controllers\v1\Operation\FinancialController::class,'create']); // create mutual fund transaction details
            Route::post('mfTraxUpdate',[App\Http\Controllers\v1\Operation\FinancialController::class,'update']); // update mutual fund transaction details
            Route::get('mfTraxDetailSearch',[App\Http\Controllers\v1\Operation\FinancialController::class,'searchDetails']); // search mutual fund transaction by name or code
            Route::post('mfTraxDetailSearch',[App\Http\Controllers\v1\Operation\FinancialController::class,'searchDetails']); // search mutual fund transaction by name or code
            Route::post('mfTraxExport',[App\Http\Controllers\v1\Operation\FinancialController::class,'export']); // export mutual fund transaction details
            
            Route::get('mfTraxFolioDetails',[App\Http\Controllers\v1\Operation\FinancialController::class,'getFolioDetails']); // get folio details based on client code

            Route::get('daysheetReport',[App\Http\Controllers\v1\Operation\ReportController::class,'index']); // show day sheet report details also use for dropdown list


            Route::get('ackPendingDetails',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'pending']); // show acknowledgement pending details also use for dropdown list
            Route::get('ackDetailSearch',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'searchDetails']); // search acknowledgement by name or code
            Route::post('ackDetailSearch',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'searchDetails']); // search acknowledgement by name or code
            Route::post('ackExport',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'export']); // export acknowledgement details
            Route::post('ackUpload',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'update']); // update acknowledgement details
            Route::post('ackFinalSubmit',[App\Http\Controllers\v1\Operation\AcknowledgementController::class,'finalSubmit']); // final submit acknowledgement details

            Route::any('manualUpdateDetailSearch',[App\Http\Controllers\v1\Operation\ManualUpdateController::class,'searchDetails']); // search manual update by name or code
            Route::post('manualUpdateExport',[App\Http\Controllers\v1\Operation\ManualUpdateController::class,'export']); // export manual update details
            Route::post('manualUpdate',[App\Http\Controllers\v1\Operation\ManualUpdateController::class,'update']); // update manual update details
            Route::post('manualUpdateFinalSubmit',[App\Http\Controllers\v1\Operation\ManualUpdateController::class,'finalSubmit']); // final submit manual update details

            // =========================== operations routes end =======================================

            // Route::post('index1',[App\Http\Controllers\v1\Master\TestController::class,'index1']);

            /*  ********************************* Start Common Report *************************** */

            
            /*  ********************************* End Common Report *************************** */


            // ==========================================Cron routes===========================================
            Route::get('nfoToOngoing',[App\Http\Controllers\v1\Cron\SchemeController::class,'nfoTOongoing']); // NFO to ongoing scheme for run manually

            
            /*********************************** start Mail Back Process ************************ */

            Route::get('mailbackFileType',[App\Http\Controllers\v1\Master\MailBackController::class,'fileType']); // get file type for mail back process
            Route::get('mailbackFileName',[App\Http\Controllers\v1\Master\MailBackController::class,'fileName']); // get file name for mail back process

            // Route::post('uploadTransDetails',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'upload']);
            Route::post('mailbackProcess',[App\Http\Controllers\v1\Master\MailBackController::class,'upload']); // upload mail back process file
            Route::any('mailbackProcessDetails',[App\Http\Controllers\v1\Master\MailBackController::class,'Details']); // get mail back process details
            Route::post('mailbackProcessPython',[App\Http\Controllers\v1\Master\MailBackController::class,'uploadusingPython']); // upload mail back process file using python script

            /*********************************** start Mail Back Process ************************ */

            /*********************************** start Mail Back Mismatch ************************ */
            Route::any('mailbackMismatch',[App\Http\Controllers\v1\Master\MailBackController::class,'misMatch']); // Mail back mismatch details
            Route::any('mailbackMismatchNAV',[App\Http\Controllers\v1\Master\MailBackController::class,'misMatchNAV']);  // NAV mismatch details
            Route::any('mailbackMismatchSipStp',[App\Http\Controllers\v1\Master\MailBackController::class,'misMatchSipStp']);  // SIP STP SWP mismatch details
            Route::any('mailbackMismatchFolio',[App\Http\Controllers\v1\Master\MailBackController::class,'misMatchFolio']);  // Folio mismatch details
            Route::any('mailbackMismatchBroker',[App\Http\Controllers\v1\Master\MailBackController::class,'misMatchBroker']);  // Broker mismatch details
        
            Route::any('misMatchNAVDelete',[App\Http\Controllers\v1\Master\MailBackController::class,'misMatchNAVDelete']);  // NAV mismatch details

            Route::post('mailbackMismatchLock',[App\Http\Controllers\v1\Master\MailBackController::class,'lockTransaction']); // lock transaction for mail back mismatch
            Route::post('mailbackMismatchUnlock',[App\Http\Controllers\v1\Master\MailBackController::class,'unlockTransaction']); // unlock transaction for mail back mismatch
            
            Route::any('mailbackMismatchAll',[App\Http\Controllers\v1\Master\MailBackController::class,'allMismatch']);  // all mismatch details

            Route::get('showISIN',[App\Http\Controllers\v1\Operation\SipStpTransController::class,'showISIN']);  // show isin no using product code

            Route::any('mailbackMismatchReplica',[App\Http\Controllers\v1\Master\MailBackReplicaController::class,'showLockTransaction']); // show all lock replicate transactions

            /*********************************** End Mail Back Mismatch ************************ */

            /*************************************************Start For file help************************************************/
            Route::any('rntTransTypeSubtype',[App\Http\Controllers\v1\Master\MFTransTypeSubTypeController::class,'Details']); // Get transaction type and subtype details
            Route::any('rntTransTypeSubtypeShow',[App\Http\Controllers\v1\Master\MFTransTypeSubTypeController::class,'index']); // Show transaction type and subtype details
            Route::post('rntTransTypeSubtypeAddEdit',[App\Http\Controllers\v1\Master\MFTransTypeSubTypeController::class,'CreateUpdate']); // Create and update transaction type and subtype details

            Route::any('rntSystematicFrequency',[App\Http\Controllers\v1\Master\SystematicFrequencyController::class,'Details']); // Get systematic frequency details
            Route::any('rntSystematicFrequencyShow',[App\Http\Controllers\v1\Master\SystematicFrequencyController::class,'index']); // Show systematic frequency details
            Route::post('rntSystematicFrequencyAddEdit',[App\Http\Controllers\v1\Master\SystematicFrequencyController::class,'CreateUpdate']); // Create and update systematic frequency details

            Route::any('rntSystematicTransType',[App\Http\Controllers\v1\Master\SystematicTransTypeController::class,'Details']); // Get systematic transaction type details
            Route::any('rntSystematicTransTypeShow',[App\Http\Controllers\v1\Master\SystematicTransTypeController::class,'index']); // Show systematic transaction type details
            Route::post('rntSystematicTransTypeAddEdit',[App\Http\Controllers\v1\Master\SystematicTransTypeController::class,'CreateUpdate']); // Create and update systematic transaction type details

            Route::any('rntSystematicUnregister',[App\Http\Controllers\v1\Master\SystematicUnregisterController::class,'Details']); // Get systematic unregister details
            Route::any('rntSystematicUnregisterShow',[App\Http\Controllers\v1\Master\SystematicUnregisterController::class,'index']); // Show systematic unregister details
            Route::post('rntSystematicUnregisterAddEdit',[App\Http\Controllers\v1\Master\SystematicUnregisterController::class,'CreateUpdate']); // Create and update systematic unregister details

            Route::any('rntFolioDetails',[App\Http\Controllers\v1\Master\FolioTaxStatusController::class,'Details']); // Get folio details
            Route::any('rntFolioDetailsShow',[App\Http\Controllers\v1\Master\FolioTaxStatusController::class,'index']); // Show folio details
            Route::post('rntFolioDetailsAddEdit',[App\Http\Controllers\v1\Master\FolioTaxStatusController::class,'CreateUpdate']); // Create and update folio details

            Route::any('fileUploadHelp',[App\Http\Controllers\v1\Master\UploadFileHelpController::class,'index']); // Show file upload help details
            Route::post('fileUploadHelpAddEdit',[App\Http\Controllers\v1\Master\UploadFileHelpController::class,'CreateUpdate']); // Create and update file upload help details

            /*************************************************End For file help************************************************/

            /*************************************************Start For Report************************************************/
            Route::any('searchClient',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'searchClient']); // Search client details
            
            Route::any('showTransDetails',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'search']); // Search Transaction details
            Route::any('showNAVDetails',[App\Http\Controllers\v1\Operation\NAVDetailsController::class,'search']);  // Search NAV details
            Route::any('showSipStpDetails',[App\Http\Controllers\v1\Operation\SipStpTransController::class,'search']);  // Search sip stp details
            Route::any('showFolioDetails',[App\Http\Controllers\v1\Operation\FolioDetailsController::class,'search']);  // Search sip stp details
            Route::any('showBrokerChangeDetails',[App\Http\Controllers\v1\Operation\BrokerChangeTransController::class,'search']);  // Search broker change details

            Route::any('showMonthlyMisReport',[App\Http\Controllers\v1\Reports\MonthlyMisController::class,'search']);  // Search Monthly MIS Report
            Route::any('showMonthlyMisTrandReport',[App\Http\Controllers\v1\Reports\MonthlyMisController::class,'searchTrands']);  // Search Monthly MIS Trands Report
            /*************************************************End For Report************************************************/

            /*************************************************Start Delete Report************************************************/

            Route::any('showDeleteTransDetails',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'searchDelete']); // Search Transaction details for delete
            Route::post('DeleteTransDetails',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'delete']); // Delete Transaction details
            Route::post('unlockTransDetails',[App\Http\Controllers\v1\Operation\TransactionDetailsController::class,'unlock']); // Unlock Transaction details
            
            /*************************************************End Delete Report************************************************/

            /*************************************************Start TAB sub TAB Show api************************************************/
            Route::any('showTab1',[App\Http\Controllers\v1\TabController::class,'Tab1']);  // Search sip stp details

            /*************************************************End TAB sub TAB Show api************************************************/

            /*************************************************Start Dashboard Report************************************************/
            Route::get('showLiveSIPAmount',[App\Http\Controllers\v1\Reports\HomeController::class,'liveSIPAmount']);  // Search sip stp details
            Route::get('showLiveSIPTrend',[App\Http\Controllers\v1\Reports\HomeController::class,'liveSIPTrend']);  // Search sip stp details
            Route::get('showCurrAum',[App\Http\Controllers\v1\Reports\HomeController::class,'currAum']);  // current aum
            Route::get('showCurrAumTrend',[App\Http\Controllers\v1\Reports\HomeController::class,'currAumTrend']);  // current aum Trend
            /*************************************************End Dashboard Report************************************************/

            // Route::prefix('client')->group(function () {

            // });
        });

    // });
});