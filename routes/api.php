<?php

use App\Http\Controllers\Api\PatientFolderController;
use App\Http\Controllers\Api\PatientLoginController;
use App\Http\Controllers\Api\PatientLogoutController;
use App\Http\Controllers\Api\PatientProfileController;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PatientRegisterController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/patient/create', [PatientRegisterController::class, 'store']);
Route::post('/patient/create/otp', [PatientRegisterController::class, 'otp']);
Route::post('/patient/login',[PatientLoginController::class, 'login']);


Route::middleware('auth:api')->group(function () {

    Route::get('/patient/profile/{id}', [PatientProfileController::class, 'profile']);
    //logout
    Route::post('/patient/logout',[PatientLogoutController::class, 'logout']);
});


//folder get, create, update, delete
Route::get('/patient/folders/{id}', [PatientFolderController::class, 'getFolders']);
Route::post('/patient/folder/create', [PatientFolderController::class, 'create']);
Route::post('/patient/folder/update', [PatientFolderController::class, 'update']);
Route::get('/patient/folder/delete/{id}', [PatientFolderController::class, 'delete']);
//file get, create, update, delete

// Route::get('/files/{folder}', [FileController::class, 'getFiles']);
