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
        });
    // });
});