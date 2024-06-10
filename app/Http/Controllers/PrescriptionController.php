<?php

namespace App\Http\Controllers;

use ArrayIterator;
use App\Models\User;
use App\Models\Drugs;
use MultipleIterator;
use App\Models\Patient;
use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PrescriptionController extends Controller
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
        $user = Auth::user();
        $doctor = $user->doctor;
        $drug = Drugs::pluck('name', 'id');

        return view('dashboard.doctor.prescription.create',[
            'doctor' => $doctor,
            'drug' => $drug

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'name' => 'required',
            'gender' => 'required',
            'age' => 'required',
            'drug.*' => 'required',
            'complaint' => 'required',

        ],[
            'drug.*.required' => 'Drug is required',
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
            return back()->withErrors($validator)->withInput();
        }

        $prescription = new Prescription();
        $user = User::where('number', $request->phone_number)->get()->first();
        if($user){
            //prescription
            $prescription->patient_id = $user->patient->id;
            $prescription->doctor_id = Auth::user()->doctor->id;
            $prescription->chief_complaint = $request->complaint;
            $advices = $request->advice;
            $implodeAdvice = '"' . implode('" "', $advices) . '"';
            $prescription->prescription_advice = $implodeAdvice;
            $tests = $request->test;
            $implodeTest = '"' . implode('" "', $tests) . '"';
            $prescription->tests = $implodeTest;
            $prescription->save();

            // mediicne
            $mi = new MultipleIterator();
            $mi->attachIterator(new ArrayIterator($request->drug));
            $mi->attachIterator(new ArrayIterator($request->type));
            $mi->attachIterator(new ArrayIterator($request->strength));
            $mi->attachIterator(new ArrayIterator($request->dose));
            $mi->attachIterator(new ArrayIterator($request->duration));
            $mi->attachIterator(new ArrayIterator($request->medicineAdvice));
            foreach ($mi as list($drug, $type, $strength, $dose, $duration, $medicineAdvice) ) {
                $medicine = new Medicine();
                $medicine->prescription_id = $prescription->id;
                $medicine->drug_id = $drug;
                $medicine->type = $type;
                $medicine->mg_ml = $strength;
                $medicine->dose = $dose;
                $medicine->duration = $duration;
                $medicine->advice = $medicineAdvice;
                $medicine->save();

            }
            return back();

        }else{


            //user
            $newUser = new User();
            $newUser->number = $request->phone_number;
            $newUser->name = $request->name;
            $newUser->role = 'patient';
            $newUser->save();
            //patient
            $patient = new Patient();
            $patient->user_id = $newUser->id;
            $patient->gender = $request->gender;

            $currentDate = date('Y-m-d');
            $birthDate = date('Y-m-d', strtotime($currentDate . ' - ' . $request->age . ' years'));
            $patient->date_of_birth = $birthDate;
            $w = $request->weight? $request->weight.',' :null;
            $h = ($request->heightFt ? $request->heightFt . '.' : '') . ($request->heightIn ? $request->heightIn : '');
            $weight_height = $w. ($h?$h : null);
            $patient->weight_height = $weight_height;
            $patient->blood_group = $request->blood_group;
            $patient->save();
            // prescription
            $prescription->patient_id = $newUser->patient->id;
            $prescription->doctor_id = Auth::user()->doctor->id;
            $prescription->chief_complaint = $request->complaint;
            $advices = $request->advice;
            $implodeAdvice = '"' . implode('" "', $advices) . '"';
            $prescription->prescription_advice = $implodeAdvice;
            $tests = $request->test;
            $implodeTest = '"' . implode('" "', $tests) . '"';
            $prescription->tests = $implodeTest;
            $prescription->save();

            // mediicne
            $mi = new MultipleIterator();
            $mi->attachIterator(new ArrayIterator($request->drug));
            $mi->attachIterator(new ArrayIterator($request->type));
            $mi->attachIterator(new ArrayIterator($request->strength));
            $mi->attachIterator(new ArrayIterator($request->dose));
            $mi->attachIterator(new ArrayIterator($request->duration));
            $mi->attachIterator(new ArrayIterator($request->medicineAdvice));
            foreach ($mi as list($drug, $type, $strength, $dose, $duration, $medicineAdvice) ) {
                $medicine = new Medicine();
                $medicine->prescription_id = $prescription->id;
                $medicine->drug_id = $drug;
                $medicine->type = $type;
                $medicine->mg_ml = $strength;
                $medicine->dose = $dose;
                $medicine->duration = $duration;
                $medicine->advice = $medicineAdvice;
                $medicine->save();

            }
            return back();
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prescription $prescription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prescription $prescription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription)
    {
        //
    }

    public function selectUsers(Request $request)
    {
        $search = $request->input('q');
        $page = $request->input('page', 1);
        $pageSize = 30; // Adjust the page size as needed

        $query = User::where('role', 'patient');

        if ($search) {
            $query->where('number', 'LIKE', "%{$search}%");
        }

        $total_count = $query->count();
        $users = $query->skip(($page - 1) * $pageSize)->take($pageSize)->get();

        $results = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->number
            ];
        });

        return response()->json([
            'items' => $results,
            'total_count' => $total_count
        ]);
    }


    public function getPatient($id){
        $user = User::where('number', $id)->get()->first();
        $patient = $user->patient;

        return response()->json([
            'user' => $user,
            'patient' => $patient
        ]);
    }
}
