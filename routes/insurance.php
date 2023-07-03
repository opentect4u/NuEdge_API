<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('ins')->group(function () {
            Route::get('type',[App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'index']);
            Route::any('typeDetailSearch',[App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'searchDetails']);
            Route::post('typeExport',[App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'export']);
            Route::post('typeAddEdit',[App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'createUpdate']);
            Route::post('typeimport', [App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'import']);
            Route::post('typeDelete', [App\Http\Controllers\v1\INSMaster\InsuranceTypeController::class,'delete']);

            Route::get('company',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'index']);
            Route::any('companyDetailSearch',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'searchDetails']);
            Route::post('companyExport',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'export']);
            Route::post('companyAddEdit',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'createUpdate']);
            Route::post('companyimport', [App\Http\Controllers\v1\INSMaster\CompanyController::class,'import']);
            Route::post('companyDelete', [App\Http\Controllers\v1\INSMaster\CompanyController::class,'delete']);

            Route::get('productType',[App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'index']);
            Route::any('productTypeDetailSearch',[App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'searchDetails']);
            Route::post('productTypeExport',[App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'export']);
            Route::post('productTypeAddEdit',[App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'createUpdate']);
            Route::post('productTypeimport', [App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'import']);
            Route::post('productTypeDelete', [App\Http\Controllers\v1\INSMaster\ProductTypeController::class,'delete']);

            Route::get('product',[App\Http\Controllers\v1\INSMaster\ProductController::class,'index']);
            Route::any('productDetailSearch',[App\Http\Controllers\v1\INSMaster\ProductController::class,'searchDetails']);
            Route::post('productExport',[App\Http\Controllers\v1\INSMaster\ProductController::class,'export']);
            Route::post('productAddEdit',[App\Http\Controllers\v1\INSMaster\ProductController::class,'createUpdate']);
            Route::post('productimport', [App\Http\Controllers\v1\INSMaster\ProductController::class,'import']);
            Route::post('productDelete', [App\Http\Controllers\v1\INSMaster\ProductController::class,'delete']);
            Route::any('productDetails', [App\Http\Controllers\v1\INSMaster\ProductController::class,'productDetails']);


            Route::get('medicalStatus',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'index']);
            Route::any('medicalStatusDetailSearch',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'searchDetails']);
            Route::post('medicalStatusExport',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'export']);
            Route::post('medicalStatusAddEdit',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'createUpdate']);
            Route::post('medicalStatusimport', [App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'import']);
            Route::post('medicalStatusDelete', [App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'delete']);

            // =======================================Start Form Received=============================
            Route::get('formreceived',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'show']);
            Route::post('formreceivedAdd',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'create']);
            Route::post('formreceivedEdit',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'update']);
            Route::post('formreceivedDelete',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'delete']);
            Route::any('formreceivedDetailSearch',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'searchDetails']);
            Route::post('formreceivedExport',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'export']);

            // =======================================End Form Reeceived==================================

            // ==========================================Start Operation =============================
            Route::get('insTraxShow',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'index']);
            // Route::get('insTraxCreateShow',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'createShow']);

            Route::post('insTraxCreate',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'create']);
            // Route::post('insTraxUpdate',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'update']);
            Route::any('insTraxDetailSearch',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'searchDetails']);
            Route::post('insTraxExport',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'export']);
            
            Route::get('insTraxFolioDetails',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'getFolioDetails']);
    
            // Route::get('daysheetReport',[App\Http\Controllers\v1\INSOperation\ReportController::class,'index']);
    
    
            Route::any('ackDetailSearch',[App\Http\Controllers\v1\INSOperation\AcknowledgementController::class,'searchDetails']);
            Route::post('ackExport',[App\Http\Controllers\v1\INSOperation\AcknowledgementController::class,'export']);
            Route::post('ackUpload',[App\Http\Controllers\v1\INSOperation\AcknowledgementController::class,'update']);
            Route::post('ackFinalSubmit',[App\Http\Controllers\v1\INSOperation\AcknowledgementController::class,'finalSubmit']);
            
            Route::any('manualUpdateDetailSearch',[App\Http\Controllers\v1\INSOperation\ManualUpdateController::class,'searchDetails']);
            Route::post('manualUpdateExport',[App\Http\Controllers\v1\INSOperation\ManualUpdateController::class,'export']);
            Route::post('manualUpdate',[App\Http\Controllers\v1\INSOperation\ManualUpdateController::class,'update']);
            Route::post('manualUpdateFinalSubmit',[App\Http\Controllers\v1\INSOperation\ManualUpdateController::class,'finalSubmit']);

            // ==========================================End Operation=============================
    
            /* ******************************** start Renew Business Opportunity *****************************/
            Route::get('businessOpportunity',[App\Http\Controllers\v1\INSOperation\BuOpportunityController::class,'index']);
            Route::post('businessOpportunityAddEdit',[App\Http\Controllers\v1\INSOperation\BuOpportunityController::class,'createUpdate']);
            // Route::post('businessOpportunityDelete',[App\Http\Controllers\v1\INSOperation\BuOpportunityController::class,'delete']);
            Route::any('businessOpportunityDetailSearch',[App\Http\Controllers\v1\INSOperation\BuOpportunityController::class,'searchDetails']);
            Route::post('businessOpportunityExport',[App\Http\Controllers\v1\INSOperation\BuOpportunityController::class,'export']);
            
            /* ******************************** start Renew Business Opportunity *****************************/

        });
    // });
});