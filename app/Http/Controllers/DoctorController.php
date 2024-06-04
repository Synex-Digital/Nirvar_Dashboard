<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Doctor_has_speciality;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $user = $doctor->user;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'number' => 'required|max:11|unique:users,number,'.$user->id,
            'degrees' => 'required',
            'specialist_id' => 'required',
        ],[
            'specialist_id.required' => 'Speciality is required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->messages() as  $messages) {
                foreach ($messages as $message) {
                    flash()->options([
                        'position' => 'bottom-right',
                    ])->error($message);
                }
            }
            $settingError = 'true';
            return back()->withErrors($validator)->withInput()->with('settingError', $settingError);
        }
        if($request->oldPassword ){
            $validator = Validator::make($request->all(), [
                'oldPassword' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (!Hash::check($value, Auth::user()->password)) {
                            $fail('The current password is incorrect.');
                        }
                    },
                ],
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required',
            ]);
            if ($validator->fails()) {
                    $errors = $validator->errors();
                    foreach ($errors->messages() as  $messages) {
                        foreach ($messages as $message) {
                            flash()->options([
                                'position' => 'bottom-right',
                            ])->error($message);
                        }
                    }
                    $settingError = 'true';
                    return back()->withErrors($validator)->withInput()->with('settingError', $settingError);
                }

        }


        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->save();
        $doctor->degrees = $request->degrees;
        $doctor->save();
        if($doctor->docHasSpec){
           $hasSpec = Doctor_has_speciality::find($doctor->docHasSpec->id);
           $hasSpec->specialist_id = $request->specialist_id;
           $hasSpec->doctor_id = $doctor->id;
           $hasSpec->save();
        }else{
            $hasSpec = new Doctor_has_speciality;
            $hasSpec->specialist_id = $request->specialist_id;
            $hasSpec->doctor_id = $doctor->id;
            $hasSpec->save();
        }

        flash()->options(['position' => 'bottom-right'])->success('Profile updated successfully');
        $activeProfile = 'true';
        return redirect(route('doctor.profile'))->with('activeProfile', $activeProfile);



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function doctorProfile()
    {
        $doctor = Auth::user()->doctor;
        $specialities = Specialist::all();
        return view('dashboard.doctor.profile',[
            'doctor' => $doctor,
            'specialities' => $specialities,
            'settingError' => 'false',
        ]);
    }
    public function doctorProfile_error()
    {
        $doctor = Auth::user()->doctor;
        $specialities = Specialist::all();
        return view('dashboard.doctor.profile',[
            'doctor' => $doctor,
            'specialities' => $specialities,
            'settingError' => 'true',
        ]);
    }


}
