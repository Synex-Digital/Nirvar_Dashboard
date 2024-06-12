<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DrugsController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\SpecialistController;

Auth::routes();


Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {return redirect(route('home')); });
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
    //prescription
    Route::get('/select/users', [PrescriptionController::class, 'selectUsers'])->name('selectUser');
    //ajax
    Route::get('/get/patient/{id}', [PrescriptionController::class, 'getPatient'])->name('getPatient');
    //doctor
    Route::get('/doctor/profile', [DoctorController::class, 'doctorProfile'])->name('doctor.profile');
    Route::get('/doctor/profile/error', [DoctorController::class, 'doctorProfile_error'])->name('doctorProfile.error');


    Route::resources([
        'doctor'=> DoctorController::class,
        'prescription'=> PrescriptionController::class,
        ]);


        });




Route::get('/sd_admin/admin/login',[AdminLoginController::class, 'login'])->name('admin.login');
Route::middleware(['admin'])->group(function () {
    Route::get('/sd_admin/admin',[AdminLoginController::class, 'index'])->name('admin.in');
    Route::resources([
        'drug'=>DrugsController::class,
        'specialist'=>SpecialistController::class,
    ]);
});
