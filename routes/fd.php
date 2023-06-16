<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('fd')->group(function () {
            Route::get('companyType',[App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'index']);
            Route::any('companyTypeDetailSearch',[App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'searchDetails']);
            Route::post('companyTypeExport',[App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'export']);
            Route::post('companyTypeAddEdit',[App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'createUpdate']);
            Route::post('companyTypeimport', [App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'import']);
            Route::post('companyTypeDelete', [App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'delete']);

            Route::get('company',[App\Http\Controllers\v1\FDMaster\CompanyController::class,'index']);
            Route::any('companyDetailSearch',[App\Http\Controllers\v1\FDMaster\CompanyController::class,'searchDetails']);
            Route::post('companyExport',[App\Http\Controllers\v1\FDMaster\CompanyController::class,'export']);
            Route::post('companyAddEdit',[App\Http\Controllers\v1\FDMaster\CompanyController::class,'createUpdate']);
            Route::post('companyimport', [App\Http\Controllers\v1\FDMaster\CompanyController::class,'import']);
            Route::post('companyDelete', [App\Http\Controllers\v1\FDMaster\CompanyController::class,'delete']);

            Route::get('scheme',[App\Http\Controllers\v1\FDMaster\SchemeController::class,'index']);
            Route::any('schemeDetailSearch',[App\Http\Controllers\v1\FDMaster\SchemeController::class,'searchDetails']);
            Route::post('schemeExport',[App\Http\Controllers\v1\FDMaster\SchemeController::class,'export']);
            Route::post('schemeAddEdit',[App\Http\Controllers\v1\FDMaster\SchemeController::class,'createUpdate']);
            Route::post('schemeimport', [App\Http\Controllers\v1\FDMaster\SchemeController::class,'import']);
            // Route::post('schemeDelete', [App\Http\Controllers\v1\FDMaster\SchemeController::class,'delete']);
            Route::post('schemeDetails', [App\Http\Controllers\v1\FDMaster\SchemeController::class,'schemeDetails']);

            Route::get('rejectReason',[App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'index']);
            Route::any('rejectReasonDetailSearch',[App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'searchDetails']);
            Route::post('rejectReasonExport',[App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'export']);
            Route::post('rejectReasonAddEdit',[App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'createUpdate']);
            Route::post('rejectReasonimport', [App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'import']);
            Route::post('rejectReasonDelete', [App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'delete']);


            // Route::get('medicalStatus',[App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'index']);
            // Route::any('medicalStatusDetailSearch',[App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'searchDetails']);
            // Route::post('medicalStatusExport',[App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'export']);
            // Route::post('medicalStatusAddEdit',[App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'createUpdate']);
            // Route::post('medicalStatusimport', [App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'import']);
            // Route::post('medicalStatusDelete', [App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'delete']);

            // =======================================Start Form Received=============================
            Route::get('formreceived',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'show']);
            Route::post('formreceivedAdd',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'create']);
            Route::post('formreceivedEdit',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'update']);
            Route::post('formreceivedDelete',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'delete']);
            Route::any('formreceivedDetailSearch',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'searchDetails']);
            Route::post('formreceivedExport',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'export']);

            // =======================================End Form Reeceived==================================

            // ==========================================Start Operation =============================
            // Route::get('fdTraxShow',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'index']);
            // Route::get('fdTraxCreateShow',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'createShow']);

            Route::post('fdTraxCreate',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'create']);
            // Route::post('fdTraxUpdate',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'update']);
            Route::any('fdTraxDetailSearch',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'searchDetails']);
            Route::post('fdTraxExport',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'export']);
            
            Route::get('fdTraxFolioDetails',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'getFolioDetails']);
    
            // Route::get('daysheetReport',[App\Http\Controllers\v1\FDOperation\ReportController::class,'index']);
    
    
            Route::any('ackDetailSearch',[App\Http\Controllers\v1\FDOperation\AcknowledgementController::class,'searchDetails']);
            Route::post('ackExport',[App\Http\Controllers\v1\FDOperation\AcknowledgementController::class,'export']);
            Route::post('ackUpload',[App\Http\Controllers\v1\FDOperation\AcknowledgementController::class,'update']);
            Route::post('ackFinalSubmit',[App\Http\Controllers\v1\FDOperation\AcknowledgementController::class,'finalSubmit']);
            
            Route::any('manualUpdateDetailSearch',[App\Http\Controllers\v1\FDOperation\ManualUpdateController::class,'searchDetails']);
            Route::post('manualUpdateExport',[App\Http\Controllers\v1\FDOperation\ManualUpdateController::class,'export']);
            Route::post('manualUpdate',[App\Http\Controllers\v1\FDOperation\ManualUpdateController::class,'update']);
            Route::post('manualUpdateFinalSubmit',[App\Http\Controllers\v1\FDOperation\ManualUpdateController::class,'finalSubmit']);

            Route::any('deliveryUpdateDetailSearch',[App\Http\Controllers\v1\FDOperation\CertificateDeliveryController::class,'searchDetails']);
            Route::post('deliveryUpdateExport',[App\Http\Controllers\v1\FDOperation\CertificateDeliveryController::class,'export']);
            Route::post('deliveryUpdate',[App\Http\Controllers\v1\FDOperation\CertificateDeliveryController::class,'update']);

            // ==========================================End Operation=============================
    
        });
    // });
});