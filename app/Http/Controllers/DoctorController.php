<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function update(Request $request, string $id)
    {
        //
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
            'error' => 'false',
        ]);
    }
    public function doctorProfile_error()
    {
        $doctor = Auth::user()->doctor;
        $specialities = Specialist::all();
        return view('dashboard.doctor.profile',[
            'doctor' => $doctor,
            'specialities' => $specialities,
            'error' => 'true',
        ]);
    }


}
