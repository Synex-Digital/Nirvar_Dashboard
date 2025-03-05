<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\helper\SmsOtp;
use App\Models\OtpVerify;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class PatientPasswordResetController extends Controller
{
    public function reset_otp(Request $request)
    {
        $request->validate([
            'number'     => 'required|digits:11|regex:/^0/',
        ]);
        $user = User::where('role', 'patient')->where('number', $request->number)->first();
        if (is_null($user)) {
            return response()->json([
                'status'    => 0,
                'message'   => "User not found",
            ], 200);
        } elseif (!is_null($user->register_at)) {
            $otp = OtpVerify::where('user_id', $user->id)->first();
            if ($otp) {
                //'otp' => rand(1000, 9999),
                $otp->otp = rand(1000, 9999);
                $otp->duration = now()->addMinutes(3);
                $otp->save();
                SmsOtp::Send($request->number, 'Otp for password reset is ' . $otp->otp . '. Expire in 3 minutes!');
            } else {
                $newotp =  OtpVerify::create([
                    'type' => 'patient',
                    'user_id' => $user->id,
                    'otp' => rand(1000, 9999),
                    //'otp' => 1234,
                    'count' => 0,
                    'duration' => now()->addMinutes(3),
                ]);
                SmsOtp::Send($request->number, 'Otp for password reset is ' . $newotp->otp . '. Expire in 3 minutes!');
            }
            return response()->json([
                'status'    => 1,
                'message'   => "OTP sent successfully",
                'data'   => [
                    'user_id' => $user->id,
                ]
            ], 200);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => "User not verified",
            ], 200);
        }
    }

    // confirm otp
    public function confirm(Request $request)
    {
        $request->validate([
            'user_id'     => 'required',
            'otp'        => 'required|digits:4',
        ]);
        $user = User::where('role', 'patient')->where('id', $request->user_id)->first();
        $otp = OtpVerify::where('user_id', $user->id)->first();
        if (is_null($user)) {
            return response()->json([
                'status'    => 0,
                'message'   => "User not found",
            ], 200);
        } else {
            if ($otp->otp != $request->otp) {
                //check otp count
                if ($otp->count >= 3) {
                    return response()->json([
                        'status' => 0,
                        'message'   => "OTP max count reached",
                        'attempt'   => $otp->count
                    ], 200);
                }
                $otp->count = $otp->count + 1;
                $otp->save();
                return response()->json([
                    'status'    => 0,
                    'message'   => "OTP not matched!",
                    'attempt'   => $otp->count
                ], 200);
            } else {
                //check otp expriy
                if ($otp->duration < now()) {
                    return response()->json([
                        'status' => 0,
                        'message'   => "OTP expired!",
                        'data'   => [
                            'user_id' => $user->id,
                        ]
                    ], 200);
                } else {
                    $otp->delete();
                    $user->remember_token = 'verified';
                    $user->save();
                    return response()->json([
                        'status'    => 1,
                        'message'   => "OTP verified successfully",
                        'user_id'   => $user->id,
                    ], 200);
                }
            }
        }
    }

    //reset password
    public function reset(Request $request)
    {


        $request->validate([
            'user_id'     => 'required',
            'password'    => 'required|min:6',
        ]);
        $user = User::where('role', 'patient')->where('id', $request->user_id)->first();
        if (is_null($user)) {
            return response()->json([
                'status'    => 0,
                'message'   => "User not found",
            ], 200);
        } elseif ($user->remember_token == 'verified') {
            $user->password = Hash::make($request->password);
            $user->remember_token = null;
            $user->save();
            return response()->json([
                'status'    => 1,
                'message'   => "Password reset successfully",
                'number'   => $user->number,
            ], 200);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => "OTP not verified",
            ], 200);
        }
    }
}
