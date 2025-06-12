<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*****************for insurence related routes************************ */
Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('ins')->group(function () {
            Route::get('type',[App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'index']); // show insurance type also use for dropdown list
            Route::any('typeDetailSearch',[App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'searchDetails']); // search insurance type by name or code
            Route::post('typeExport',[App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'export']); // export insurance type
            Route::post('typeAddEdit',[App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'createUpdate']); // create and update insurance type
            Route::post('typeimport', [App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'import']); // import insurance type
            Route::post('typeDelete', [App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'delete']); // delete insurance type

            Route::get('company',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'index']); // show company details also use for dropdown list
            Route::any('companyDetailSearch',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'searchDetails']); // search company by name or code
            Route::post('companyExport',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'export']); // export company details
            Route::post('companyAddEdit',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'createUpdate']); // create and update company details
            Route::post('companyimport', [App\Http\Controllers\v1\INSMaster\CompanyController::class,'import']); // import company details
            Route::post('companyDelete', [App\Http\Controllers\v1\INSMaster\CompanyController::class,'delete']); // delete company details

            Route::get('productType',[App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'index']); // show product type details also use for dropdown list
            Route::any('productTypeDetailSearch',[App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'searchDetails']); // search product type by name or code
            Route::post('productTypeExport',[App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'export']); // export product type details
            Route::post('productTypeAddEdit',[App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'createUpdate']); // create and update product type details
            Route::post('productTypeimport', [App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'import']); // import product type details
            Route::post('productTypeDelete', [App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'delete']); // delete product type details

            Route::get('product',[App\Http\Controllers\v1\INSMaster\ProductController::class,'index']); // show product details also use for dropdown list
            Route::any('productDetailSearch',[App\Http\Controllers\v1\INSMaster\ProductController::class,'searchDetails']); // search product by name or code
            Route::post('productExport',[App\Http\Controllers\v1\INSMaster\ProductController::class,'export']); // export product details
            Route::post('productAddEdit',[App\Http\Controllers\v1\INSMaster\ProductController::class,'createUpdate']); // create and update product details
            Route::post('productimport', [App\Http\Controllers\v1\INSMaster\ProductController::class,'import']); // import product details
            Route::post('productDelete', [App\Http\Controllers\v1\INSMaster\ProductController::class,'delete']); // delete product details
            Route::any('productDetails', [App\Http\Controllers\v1\INSMaster\ProductController::class,'productDetails']); // get product details by product id


            Route::get('medicalStatus',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'index']); // show medical status also use for dropdown list
            Route::any('medicalStatusDetailSearch',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'searchDetails']); // search medical status by name or code
            Route::post('medicalStatusExport',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'export']); // export medical status
            Route::post('medicalStatusAddEdit',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'createUpdate']);  // create and update medical status
            Route::post('medicalStatusimport', [App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'import']);    // import medical status
            Route::post('medicalStatusDelete', [App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'delete']);    // delete medical status

            // =======================================Start Form Received=============================
            Route::get('formreceived',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'show']); // show form received
            Route::post('formreceivedAdd',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'create']); // create form received
            Route::post('formreceivedEdit',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'update']); // update form received
            Route::post('formreceivedDelete',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'delete']); // delete form received
            Route::any('formreceivedDetailSearch',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'searchDetails']); // search form received by name or code
            Route::post('formreceivedExport',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'export']); // export form received

            // =======================================End Form Reeceived==================================

            // ==========================================Start Operation =============================
            Route::get('insTraxShow',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'index']); // show form entry details
            // Route::get('insTraxCreateShow',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'createShow']);

            Route::post('insTraxCreate',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'create']); // create form entry
            // Route::post('insTraxUpdate',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'update']);
            Route::any('insTraxDetailSearch',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'searchDetails']); // search form entry by name or code
            Route::post('insTraxExport',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'export']); // export form entry details
            
            Route::get('insTraxFolioDetails',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'getFolioDetails']); // get folio details for form entry
    
            // Route::get('daysheetReport',[App\Http\Controllers\v1\INSOperation\ReportController::class,'index']);
    
    
            Route::any('ackDetailSearch',[App\Http\Controllers\v1\INSOperation\AcknowledgementController::class,'searchDetails']); // search acknowledgement by name or code
            Route::post('ackExport',[App\Http\Controllers\v1\INSOperation\AcknowledgementController::class,'export']); // export acknowledgement details
            Route::post('ackUpload',[App\Http\Controllers\v1\INSOperation\AcknowledgementController::class,'update']); // update acknowledgement details
            Route::post('ackFinalSubmit',[App\Http\Controllers\v1\INSOperation\AcknowledgementController::class,'finalSubmit']); // final submit acknowledgement details
            
            Route::any('manualUpdateDetailSearch',[App\Http\Controllers\v1\INSOperation\ManualUpdateController::class,'searchDetails']); // search manual update by name or code
            Route::post('manualUpdateExport',[App\Http\Controllers\v1\INSOperation\ManualUpdateController::class,'export']); // export manual update details
            Route::post('manualUpdate',[App\Http\Controllers\v1\INSOperation\ManualUpdateController::class,'update']); // update manual update details
            Route::post('manualUpdateFinalSubmit',[App\Http\Controllers\v1\INSOperation\ManualUpdateController::class,'finalSubmit']); // final submit manual update details

            // ==========================================End Operation=============================
    
            /* ******************************** start Renew Business Opportunity *****************************/
            Route::get('businessOpportunity',[App\Http\Controllers\v1\INSOperation\BuOpportunityController::class,'index']); // show business opportunity also use for dropdown list
            Route::post('businessOpportunityAddEdit',[App\Http\Controllers\v1\INSOperation\BuOpportunityController::class,'createUpdate']); // create and update business opportunity
            // Route::post('businessOpportunityDelete',[App\Http\Controllers\v1\INSOperation\BuOpportunityController::class,'delete']);
            Route::any('businessOpportunityDetailSearch',[App\Http\Controllers\v1\INSOperation\BuOpportunityController::class,'searchDetails']); // search business opportunity by name or code
            Route::post('businessOpportunityExport',[App\Http\Controllers\v1\INSOperation\BuOpportunityController::class,'export']); // export business opportunity details
            
            /* ******************************** start Renew Business Opportunity *****************************/

        });
    // });
});