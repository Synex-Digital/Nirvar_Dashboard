<?php

use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DrugsController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\AdminLogoutController;
use App\Http\Controllers\admin\AdminRegisterController;
use App\Http\Controllers\MailController;

Auth::routes();


Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return redirect(route('home'));
    });
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
    //prescription
    Route::get('/select/users', [PrescriptionController::class, 'selectUsers'])->name('selectUser');
    Route::get('/prescription/preview/{slug}', [PrescriptionController::class, 'prescriptionPreview'])->name('prescriptionpreview');
    //ajax
    Route::get('/get/patient/{id}', [PrescriptionController::class, 'getPatient'])->name('getPatient');
    //doctor
    Route::get('/doctor/profile', [DoctorController::class, 'doctorProfile'])->name('doctor.profile');
    Route::get('/doctor/profile/error', [DoctorController::class, 'doctorProfile_error'])->name('doctorProfile.error');
    Route::post('/mail/prescription', [MailController::class, 'mail'])->name('mail.prescription');

    Route::resources([
        'doctor' => DoctorController::class,
        'prescription' => PrescriptionController::class,
    ]);
});




Route::post('sd_admin/login/dashboard', [AdminLoginController::class, 'loginAdminForm'])->name('adminLoginDashboard');
Route::get('sd_admin/login', [AdminLoginController::class, 'login'])->name('adminLogin');
Route::get('sd_admin/register', function () {
    if (DB::table('admins')->get()->count() > 0) {
        return redirect()->route('adminLogin');
    }
    return (new AdminRegisterController())->register();
})->name('adminRegister');
Route::post('sd_admin/register/store', [AdminRegisterController::class, 'register_store'])->name('adminRegisterStore');

Route::middleware(['admin'])->group(function () {

    Route::get('/admin/patient', [PatientController::class, 'adminPatient'])->name('adminPatient');
    Route::get('/admin/doctor', [DoctorController::class, 'adminDoctor'])->name('adminDoctor');
    Route::get('/admin/prescriptions', [PrescriptionController::class, 'adminPrescriptionShow'])->name('adminPrescriptionShow');
    Route::get('/admin/prescription/preview{slug}', [PrescriptionController::class, 'adminPrescriptionPreview'])->name('adminPrescriptionPreview');



    Route::post('/sd_admin/logout', [AdminLogoutController::class, 'logout'])->name('adminLogout');
    Route::resources([
        'drug' => DrugsController::class,
        'specialist' => SpecialistController::class,
        'admin' => AdminController::class,
    ]);
});
