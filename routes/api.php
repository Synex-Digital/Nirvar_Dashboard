<?php

use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PatientFileController;
use App\Http\Controllers\Api\PatientLoginController;
use App\Http\Controllers\Api\BloodPressureController;
use App\Http\Controllers\Api\DiabetesController;
use App\Http\Controllers\Api\FolderShareController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\PatientFolderController;
use App\Http\Controllers\Api\PatientLogoutController;
use App\Http\Controllers\Api\PatientProfileController;

use App\Http\Controllers\Api\PatientRegisterController;
use App\Http\Controllers\Api\PatientPasswordResetController;
use App\Http\Controllers\Api\SpecialityController;

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
//?
//push notification token
Route::post('/patient/fcm-token', [PatientLoginController::class, 'get_fcm_token']);
//folder share

Route::get('/folder/{folder}', [FolderShareController::class, 'access'])->name('folder.access')->middleware('signed');
//file view
Route::get('/image/{fileId}', [FolderShareController::class, 'show'])
     ->name('image.show')
     ->middleware('signed');

Route::get('/patient/file/{filename}',[FolderShareController::class, 'generateImage'])->name('file.share');
Route::get('/file/{filename}', [FolderShareController::class, 'imageAccess'])
     ->name('image.access')
     ->middleware('signed');

Route::middleware('auth:api')->group(function () {
    //folder share
    Route::get('/patient/folders/{folder}/share',[FolderShareController::class, 'share']);
    //profile
    Route::get('/patient/profile/{id}', [PatientProfileController::class, 'profile']);
    Route::post('/patient/password/change', [PatientProfileController::class, 'password_change']);
    //logout
    Route::post('/patient/logout',[PatientLogoutController::class, 'logout']);
    //profile register
    Route::post('/patient/profile/register', [PatientProfileController::class, 'profile_register']);
    //profile update
    Route::post('/patient/profile/update', [PatientProfileController::class, 'profile_update']);
    //get search data
    Route::post('/patient/search', [PatientProfileController::class, 'search']);
    //folder get, create, update, delete

    Route::get('/patient/select/folders', [PatientFolderController::class, 'selectFolders']);
    Route::get('/patient/folders', [PatientFolderController::class, 'getFolders']);
    Route::post('/patient/folder/create', [PatientFolderController::class, 'create']);
    Route::post('/patient/folder/update', [PatientFolderController::class, 'update']);
    Route::get('/patient/folder/delete/{id}', [PatientFolderController::class, 'delete']);
    //file get, rename,upload, delete, download
    Route::get('/patient/files/{id}',[PatientFileController::class, 'getFiles']);
    Route::post('/patient/file/upload', [PatientFileController::class, 'upload']);
    Route::post('/patient/file/rename', [PatientFileController::class, 'rename']);
    Route::get('/patient/file/delete/{id}', [PatientFileController::class, 'delete']);
    Route::get('/patient/file/download/{id}', [PatientFileController::class, 'download']);
    Route::get('/patient/files/latest/two',[PatientFileController::class, 'latest']);
    //speciality
    Route::get('/patient/doctor/specialities', [SpecialityController::class, 'specialities']);

    //micro service tool

    //blood pressure
    Route::get('/patient/blood-pressure/today', [BloodPressureController::class, 'blood_pressure_today']);
    Route::get('/patient/blood-pressure/past-seven-days', [BloodPressureController::class, 'blood_pressure_seven_days']);
    Route::get('/patient/blood-pressure/weekly', [BloodPressureController::class, 'blood_pressure_weekly']);
    Route::get('/patient/blood-pressure/monthly', [BloodPressureController::class, 'blood_pressure_monthly']);
    Route::post('/patient/blood-pressure/store', [BloodPressureController::class, 'blood_pressure_store']);
    // diabetes
    Route::get('/patient/diabetes/today', [DiabetesController::class, 'diabetes_today']);
    Route::get('/patient/diabetes/past-seven-days', [DiabetesController::class, 'diabetes_seven_days']);
    Route::get('/patient/diabetes/weekly', [DiabetesController::class, 'diabetes_weekly']);
    Route::get('/patient/diabetes/monthly', [DiabetesController::class, 'diabetes_monthly']);
    Route::post('/patient/diabetes/store', [DiabetesController::class, 'diabetes_store']);
    // notification
    Route::get('/patient/notifications', [NotificationsController::class, 'getNotifications']);
    Route::post('/patient/read/notification', [NotificationsController::class, 'readNotification']);
});

