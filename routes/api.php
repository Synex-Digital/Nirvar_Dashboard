<?php

use App\Http\Controllers\Api\PatientFileController;
use App\Http\Controllers\Api\PatientFolderController;
use App\Http\Controllers\Api\PatientLoginController;
use App\Http\Controllers\Api\PatientLogoutController;
use App\Http\Controllers\Api\PatientPasswordResetController;
use App\Http\Controllers\Api\PatientProfileController;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PatientRegisterController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/patient/register', [PatientRegisterController::class, 'store']);
Route::post('/patient/register/otp', [PatientRegisterController::class, 'otp']);
Route::post('/patient/resend/otp', [PatientRegisterController::class, 'resend_otp']);
Route::post('/patient/password/reset/otp', [PatientPasswordResetController::class, 'reset_otp']);
Route::post('/patient/password/reset/confirm', [PatientPasswordResetController::class, 'confirm']);
Route::post('/patient/password/reset', [PatientPasswordResetController::class, 'reset']);

Route::post('/patient/login',[PatientLoginController::class, 'login']);


Route::middleware('auth:api')->group(function () {
});
Route::get('/patient/profile/{id}', [PatientProfileController::class, 'profile']);
//profile
//logout
Route::post('/patient/logout',[PatientLogoutController::class, 'logout']);
//profile update
Route::post('/patient/profile/update', [PatientProfileController::class, 'profile_update']);
//folder get, create, update, delete
Route::get('/patient/folders', [PatientFolderController::class, 'getFolders']);
Route::post('/patient/folder/create', [PatientFolderController::class, 'create']);
Route::post('/patient/folder/update', [PatientFolderController::class, 'update']);
Route::get('/patient/folder/delete/{id}', [PatientFolderController::class, 'delete']);
//file get, upload, delete
Route::get('/patient/files/{id}',[PatientFileController::class, 'getFiles']);
Route::post('/patient/file/upload', [PatientFileController::class, 'upload']);
Route::get('/patient/file/delete/{id}', [PatientFileController::class, 'delete']);
