<?php

use App\Http\Controllers\Api\PatientRegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/patient/create', [PatientRegisterController::class, 'store']);
Route::post('/patient/create/otp', [PatientRegisterController::class, 'otp']);
