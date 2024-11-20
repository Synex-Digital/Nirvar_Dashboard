<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Diabetes;
use Illuminate\Http\Request;
use App\Models\BloodPressure;
use App\Models\NotificationToken;
use Illuminate\Support\Facades\Auth;

class  HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $doctor = $user->doctor;
        $notification_count = 0;
        $doctor->degrees == null ? $notification_count ++ : '';
        // // $doctor->description == null ? $count++ : '';
        $doctor->docHasSpec == null ? $notification_count++ : '';
        if ($doctor->degrees == null || !$doctor->docHasSpec()->exists()	 ) {
            $notification_count++;
        }
        $prescriptionCount = $doctor->prescription? $doctor->prescription->count() : 0;
        $patientCount = $doctor->prescription->pluck('patient_id')->unique()->count();




        return view('dashboard.doctor.index',[
            'count' => $notification_count,
            'prescriptionCount' => $prescriptionCount,
            'patientCount' => $patientCount,
        ]);
    }
}
