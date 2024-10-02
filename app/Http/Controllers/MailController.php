<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\NewPrescription;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class MailController extends Controller
{
    function mail(Request $request)
    {
        $prescription = Prescription::find($request->prescription_id);

        if($prescription){
            $this->sendMail($request->email, $prescription);
            flash()->options(['position' => 'bottom-right'])->success('Sent Successfully');
            return back();
        }else{
            flash()->options(['position' => 'bottom-right'])->error(' Prescription not found');
            return back();
        }
    }

    protected function sendMail($mail, $data = null)
    {
        Mail::to($mail)->send(new NewPrescription($data));
    }
}
