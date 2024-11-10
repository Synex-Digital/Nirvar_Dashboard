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
use Kreait\Firebase\Factory;
use App\Mail\NewPrescription;
use App\Models\NotificationToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Jobs\GeneratePrescriptionPdf;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;

class PrescriptionController extends Controller
{





    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // $prescription = Auth::user()->doctor->prescription()->paginate(15);
        $prescription = Prescription::where('doctor_id', Auth::user()->doctor->id)->whereBetween('created_at', [now()->subDays(7), now()])->orderBy('created_at', 'desc')->paginate(15);
        return view('dashboard.doctor.prescription.list', [

            'prescription' => $prescription
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $doctor = $user->doctor;
        $drug = Drugs::pluck('name', 'id');

        return view('dashboard.doctor.prescription.create', [
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

        ], [
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
        $user = User::where('role', 'patient')->where('number', $request->phone_number)->get()->first();
        if ($user) {
            //prescription
            $prescription->patient_id = $user->patient->id;
            $prescription->doctor_id = Auth::user()->doctor->id;
            $prescription->chief_complaint = $request->complaint;
            //advice
            $advice = $request->input('advice');
            $filteredAdvice = array_filter($advice, function ($value) {
                return !is_null($value);
            });
            if (empty($filteredAdvice)) {
                $implodeAdvice = null;
            } else {
                $implodeAdvice = '"' . implode('" "', $filteredAdvice) . '"';
            }
            $prescription->prescription_advice = $implodeAdvice;
            //tests
            $tests = $request->input('test');
            $filteredTest = array_filter($tests, function ($value) {
                return !is_null($value);
            });
            if (empty($filteredTest)) {
                $implodeTest = null;
            } else {
                $implodeTest = '"' . implode('" "', $filteredTest) . '"';
            }
            $prescription->tests = $implodeTest;
            $prescription->reference = 'NRVR-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 6);
            $prescription->save();

            // mediicne
            $mi = new MultipleIterator();
            $mi->attachIterator(new ArrayIterator($request->drug));
            $mi->attachIterator(new ArrayIterator($request->type));
            $mi->attachIterator(new ArrayIterator($request->strength));
            $mi->attachIterator(new ArrayIterator($request->dose));
            $mi->attachIterator(new ArrayIterator($request->duration));
            $mi->attachIterator(new ArrayIterator($request->medicineAdvice));
            foreach ($mi as list($drug, $type, $strength, $dose, $duration, $medicineAdvice)) {
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

            // $doctors = Auth::user()->doctor;
            // $patients = $user->patient;
            $prescriptions = Prescription::find($prescription->id);
            GeneratePrescriptionPdf::dispatch($prescriptions);
            $this->sendPrescriptionNotification($user->id);
            return redirect()->route('prescriptionpreview', ['slug' => $prescriptions->reference]);


        } else {
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
            //weight and height
            $weight_height = null;
            $w = $request->weight ? $request->weight : null;
            $h = ($request->heightFt ? $request->heightFt . '.' : '') . ($request->heightIn ? $request->heightIn : '');
            if ($request->heightFt) {
                if ($request->heightIn) {
                    $h = $request->heightFt . '.' . $request->heightIn;
                } else {
                    $h = $request->heightFt;
                }
            } elseif ($request->heightIn) {
                $h = $request->heightIn;
            }
            if ($w) {
                if ($h) {
                    $weight_height = $w . ',' . $h;
                } else {
                    $weight_height = $w;
                }
            } elseif ($h) {
                $weight_height = $h;
            }
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
            $implodeTest = $tests ?  '"' . implode('" "', $tests) . '"' : null;
            $prescription->tests = $implodeTest;
            $prescription->reference = 'NRVR-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 6);
            $prescription->save();

            // mediicne
            $mi = new MultipleIterator();
            $mi->attachIterator(new ArrayIterator($request->drug));
            $mi->attachIterator(new ArrayIterator($request->type));
            $mi->attachIterator(new ArrayIterator($request->strength));
            $mi->attachIterator(new ArrayIterator($request->dose));
            $mi->attachIterator(new ArrayIterator($request->duration));
            $mi->attachIterator(new ArrayIterator($request->medicineAdvice));
            foreach ($mi as list($drug, $type, $strength, $dose, $duration, $medicineAdvice)) {
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
            // $doctors = Auth::user()->doctor;
            // $patients = Patient::find($patient->id);
            $prescriptions = Prescription::find($prescription->id);
            //pdf

            GeneratePrescriptionPdf::dispatch($prescriptions);
            $this->sendPrescriptionNotification($newUser->id);
            return redirect()->route('prescriptionpreview', ['slug' => $prescriptions->reference]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription)
    {
        $pre_check = Prescription::where('id', $prescription->id)->where('doctor_id', Auth::user()->doctor->id)->whereBetween('created_at', [now()->subDays(7), now()])->get();
        $pre_array = $pre_check->toArray();
        $array = array_filter($pre_array, function ($value) {
            return !is_null($value);
        });
        if (empty($array)) {
            return back();
        } else {
            $doctors = Auth::user()->doctor;
            $patients = $prescription->patient;

            return view('dashboard.doctor.prescription.preview', [
                'doctors' => $doctors,
                'patients' => $patients,
                'prescriptions' => $prescription,
            ]);
        }
        // if(trim($pre_check) != null){
        //     $doctors = Auth::user()->doctor;
        //     $patients = $prescription->patient;

        //     return view('dashboard.doctor.prescription.preview',[
        //         'doctors' => $doctors,
        //         'patients' => $patients,
        //         'prescriptions' => $prescription,
        //     ]);
        // }
        // else{
        //
        // }

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
        if($user->role == 'patient'){
            $patient = $user->patient;
        }else{
            $patient = 'false';
        }

        return response()->json([
            'user' => $user,
            'patient' => $patient
        ]);
    }

    public function prescriptionPreview($slug){

        // preview the prescription
        $prescriptions = Prescription::where('reference', $slug)->get()->first();
        $doctors = $prescriptions->doctor;
        $patients = $prescriptions->patient;
         return view('dashboard.doctor.prescription.preview', [
                'doctors' => $doctors,
                'patients' => $patients,
                'prescriptions' => $prescriptions,
            ]);
    }

    public function adminPrescriptionShow(){
        $prescriptions = Prescription::orderBy('created_at', 'desc')->paginate(15);
        return view('dashboard.admin.prescription.list',[
            'prescriptions' => $prescriptions
        ]);
    }
    public function adminPrescriptionPreview($slug){

        $prescription = Prescription::where('reference', $slug)->get()->first();
        $doctors = $prescription->doctor;
        $patients = $prescription->patient;
        return view('dashboard.admin.prescription.preview',[
            'prescriptions' => $prescription,
            'doctors' => $doctors,
            'patients' => $patients
        ]);
    }
    public function Preview($slug){

        $prescription = Prescription::where('reference', 'NRVR-F17UBD')->get()->first();
        GeneratePrescriptionPdf::dispatch($prescription);
    }

    protected function mail($mail, $data = null)
    {
        Mail::to($mail)->send(new NewPrescription($data));
    }


    public function sendPrescriptionNotification($userId)
{
    // Get the patient's FCM token
    $patient = User::find($userId);
    if (!$patient) {
        return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
    }

    $notificationToken = NotificationToken::where('user_id', $patient->id)->first();
    if (!$notificationToken || !$notificationToken->device_token) {
        return response()->json(['status' => 'error', 'message' => 'FCM token not found for user.'], 404);
    }

    $fcmToken = $notificationToken->device_token;

    // Initialize Firebase with the service account credentials
    $firebase = (new Factory)
    ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
    ->create();

    $messaging = $firebase->getMessaging();

    // Create the Notification object
    $notification = Notification::create('New Prescription', 'A new prescription has been created for you.');

    // Create the notification message
    $message = CloudMessage::withTarget('token', $fcmToken)
        ->withNotification($notification);

    try {
        // Send the notification
        $messaging->send($message);
        return response()->json(['status' => 'success', 'message' => 'Notification sent successfully.']);
    } catch (MessagingException $e) {
        // Log error and handle any exceptions
        Log::error('FCM Messaging Error: ' . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}

}
