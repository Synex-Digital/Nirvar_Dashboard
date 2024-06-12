<?php

namespace App\Http\Controllers;

use App\Mail\NewPrescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class MailController extends Controller
{
    function mail(Request $request)
    {
        $this->sendMail($request->email);
        return back();
    }

    protected function sendMail($mail, $data = null)
    {
        Mail::to($mail)->send(new NewPrescription($data));
    }
}
