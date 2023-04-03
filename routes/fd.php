<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('fd')->group(function () {
            Route::get('companyType',[App\Http\Controllers\v1\INSMaster\InsurancecompanyTypeController::class,'index']);
            Route::any('companyTypeDetailSearch',[App\Http\Controllers\v1\INSMaster\InsurancecompanyTypeController::class,'searchDetails']);
            Route::post('companyTypeExport',[App\Http\Controllers\v1\INSMaster\InsurancecompanyTypeController::class,'export']);
            Route::post('companyTypeAddEdit',[App\Http\Controllers\v1\INSMaster\InsurancecompanyTypeController::class,'createUpdate']);
            Route::post('companyTypeimport', [App\Http\Controllers\v1\INSMaster\InsurancecompanyTypeController::class,'import']);
            Route::post('companyTypeDelete', [App\Http\Controllers\v1\INSMaster\InsurancecompanyTypeController::class,'delete']);

            Route::get('company',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'index']);
            Route::any('companyDetailSearch',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'searchDetails']);
            Route::post('companyExport',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'export']);
            Route::post('companyAddEdit',[App\Http\Controllers\v1\INSMaster\CompanyController::class,'createUpdate']);
            Route::post('companyimport', [App\Http\Controllers\v1\INSMaster\CompanyController::class,'import']);
            Route::post('companyDelete', [App\Http\Controllers\v1\INSMaster\CompanyController::class,'delete']);

            Route::get('scheme',[App\Http\Controllers\v1\INSMaster\schemeController::class,'index']);
            Route::any('schemeDetailSearch',[App\Http\Controllers\v1\INSMaster\schemeController::class,'searchDetails']);
            Route::post('schemeExport',[App\Http\Controllers\v1\INSMaster\schemeController::class,'export']);
            Route::post('schemeAddEdit',[App\Http\Controllers\v1\INSMaster\schemeController::class,'createUpdate']);
            Route::post('schemeimport', [App\Http\Controllers\v1\INSMaster\schemeController::class,'import']);
            // Route::post('schemeDelete', [App\Http\Controllers\v1\INSMaster\schemeController::class,'delete']);
            Route::post('schemeDetails', [App\Http\Controllers\v1\INSMaster\schemeController::class,'schemeDetails']);


            // Route::get('medicalStatus',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'index']);
            // Route::any('medicalStatusDetailSearch',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'searchDetails']);
            // Route::post('medicalStatusExport',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'export']);
            // Route::post('medicalStatusAddEdit',[App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'createUpdate']);
            // Route::post('medicalStatusimport', [App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'import']);
            // Route::post('medicalStatusDelete', [App\Http\Controllers\v1\INSMaster\MedicalStatusController::class,'delete']);

            // =======================================Start Form Received=============================
            Route::get('formreceived',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'show']);
            Route::post('formreceivedAdd',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'create']);
            Route::post('formreceivedEdit',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'update']);
            Route::post('formreceivedDelete',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'delete']);
            Route::any('formreceivedDetailSearch',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'searchDetails']);
            Route::post('formreceivedExport',[App\Http\Controllers\v1\INSOperation\FormReceivedController::class,'export']);

            // =======================================End Form Reeceived==================================

            // ==========================================Start Operation =============================
            // Route::get('insTraxShow',[App\Http\Controllers\v1\INSOperation\FormEntryController::class,'index']);
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
    
        });
    // });
});