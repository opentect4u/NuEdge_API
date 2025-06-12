<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*****************for fixed deposit related routes************************ */
Route::prefix('v1')->group(function () {
    // Route::middleware(['ipcheck'])->group(function () {
        Route::prefix('fd')->group(function () {
            Route::get('companyType',[App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'index']); // show company type also use for dropdown list
            Route::any('companyTypeDetailSearch',[App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'searchDetails']); // search company type by name or code
            Route::post('companyTypeExport',[App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'export']);  // export company type
            Route::post('companyTypeAddEdit',[App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'createUpdate']); // create and update company type
            Route::post('companyTypeimport', [App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'import']); // import company type
            Route::post('companyTypeDelete', [App\Http\Controllers\v1\FDMaster\CompanyTypeController::class,'delete']); // delete company type

            Route::get('company',[App\Http\Controllers\v1\FDMaster\CompanyController::class,'index']); // show company details also use for dropdown list
            Route::any('companyDetailSearch',[App\Http\Controllers\v1\FDMaster\CompanyController::class,'searchDetails']); // search company by name or code
            Route::post('companyExport',[App\Http\Controllers\v1\FDMaster\CompanyController::class,'export']); // export company details
            Route::post('companyAddEdit',[App\Http\Controllers\v1\FDMaster\CompanyController::class,'createUpdate']); // create and update company details
            Route::post('companyimport', [App\Http\Controllers\v1\FDMaster\CompanyController::class,'import']); // import company details
            Route::post('companyDelete', [App\Http\Controllers\v1\FDMaster\CompanyController::class,'delete']); // delete company details

            Route::get('scheme',[App\Http\Controllers\v1\FDMaster\SchemeController::class,'index']); // show scheme details also use for dropdown list
            Route::any('schemeDetailSearch',[App\Http\Controllers\v1\FDMaster\SchemeController::class,'searchDetails']); // search scheme by name or code
            Route::post('schemeExport',[App\Http\Controllers\v1\FDMaster\SchemeController::class,'export']); // export scheme details
            Route::post('schemeAddEdit',[App\Http\Controllers\v1\FDMaster\SchemeController::class,'createUpdate']); // create and update scheme details
            Route::post('schemeimport', [App\Http\Controllers\v1\FDMaster\SchemeController::class,'import']); // import scheme details
            Route::post('schemeDelete', [App\Http\Controllers\v1\FDMaster\SchemeController::class,'delete']); // delete scheme details
            // Route::post('schemeDetails', [App\Http\Controllers\v1\FDMaster\SchemeController::class,'schemeDetails']);

            Route::get('rejectReason',[App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'index']); // show reject reason also use for dropdown list
            Route::any('rejectReasonDetailSearch',[App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'searchDetails']); // search reject reason by name or code
            Route::post('rejectReasonExport',[App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'export']); // export reject reason
            Route::post('rejectReasonAddEdit',[App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'createUpdate']); // create and update reject reason
            Route::post('rejectReasonimport', [App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'import']); // import reject reason
            Route::post('rejectReasonDelete', [App\Http\Controllers\v1\FDMaster\RejectReasonController::class,'delete']); // delete reject reason


            // Route::get('medicalStatus',[App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'index']);
            // Route::any('medicalStatusDetailSearch',[App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'searchDetails']);
            // Route::post('medicalStatusExport',[App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'export']);
            // Route::post('medicalStatusAddEdit',[App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'createUpdate']);
            // Route::post('medicalStatusimport', [App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'import']);
            // Route::post('medicalStatusDelete', [App\Http\Controllers\v1\FDMaster\MedicalStatusController::class,'delete']);

            // =======================================Start Form Received=============================
            Route::get('formreceived',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'show']); // show form received 
            Route::post('formreceivedAdd',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'create']); // create form received
            Route::post('formreceivedEdit',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'update']); // update form received
            Route::post('formreceivedDelete',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'delete']); // delete form received
            Route::any('formreceivedDetailSearch',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'searchDetails']); // search form received by name or code
            Route::post('formreceivedExport',[App\Http\Controllers\v1\FDOperation\FormReceivedController::class,'export']); // export form received

            // =======================================End Form Reeceived==================================

            // ==========================================Start Operation =============================
            Route::get('fdTraxShow',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'index']); // show form entry details
            // Route::get('fdTraxCreateShow',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'createShow']);

            Route::post('fdTraxCreate',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'create']); // create form entry
            // Route::post('fdTraxUpdate',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'update']);
            Route::any('fdTraxDetailSearch',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'searchDetails']); // search form entry by name or code
            Route::post('fdTraxExport',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'export']); // export form entry details
            
            Route::get('fdTraxFolioDetails',[App\Http\Controllers\v1\FDOperation\FormEntryController::class,'getFolioDetails']); // get folio details for form entry
    
            // Route::get('daysheetReport',[App\Http\Controllers\v1\FDOperation\ReportController::class,'index']);
    
    
            Route::any('ackDetailSearch',[App\Http\Controllers\v1\FDOperation\AcknowledgementController::class,'searchDetails']); // search acknowledgement by name or code
            Route::post('ackExport',[App\Http\Controllers\v1\FDOperation\AcknowledgementController::class,'export']); // export acknowledgement details
            Route::post('ackUpload',[App\Http\Controllers\v1\FDOperation\AcknowledgementController::class,'update']); // update acknowledgement details
            Route::post('ackFinalSubmit',[App\Http\Controllers\v1\FDOperation\AcknowledgementController::class,'finalSubmit']); // final submit acknowledgement details
            
            Route::any('manualUpdateDetailSearch',[App\Http\Controllers\v1\FDOperation\ManualUpdateController::class,'searchDetails']); // search manual update by name or code
            Route::post('manualUpdateExport',[App\Http\Controllers\v1\FDOperation\ManualUpdateController::class,'export']); // export manual update details
            Route::post('manualUpdate',[App\Http\Controllers\v1\FDOperation\ManualUpdateController::class,'update']); // update manual update details
            Route::post('manualUpdateFinalSubmit',[App\Http\Controllers\v1\FDOperation\ManualUpdateController::class,'finalSubmit']); // final submit manual update details

            Route::any('deliveryUpdateDetailSearch',[App\Http\Controllers\v1\FDOperation\CertificateDeliveryController::class,'searchDetails']); // search delivery update by name or code
            Route::post('deliveryUpdateExport',[App\Http\Controllers\v1\FDOperation\CertificateDeliveryController::class,'export']); // export delivery update details
            Route::post('deliveryUpdate',[App\Http\Controllers\v1\FDOperation\CertificateDeliveryController::class,'update']); // update delivery update details

            // ==========================================End Operation=============================
    
        });
    // });
});