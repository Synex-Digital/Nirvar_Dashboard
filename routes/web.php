<?php

use App\Http\Controllers\DoctorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;




Auth::routes();


Route::middleware(['auth'])->group(function () {

Route::get('/', function () {return redirect(route('home')); });
Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
//doctor
Route::get('/doctor/profile', [DoctorController::class, 'doctorProfile'])->name('doctor.profile');


Route::resources([
    'doctor'=> DoctorController::class,
]);


});
