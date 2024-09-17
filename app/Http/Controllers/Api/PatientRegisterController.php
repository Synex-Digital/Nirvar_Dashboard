<?php

namespace App\Http\Controllers\Api;


use App\Models\User;
use App\Models\Patient;
use App\Models\OtpVerify;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helper\SmsOtp;
use Illuminate\Validation\ValidationException;

class PatientRegisterController extends Controller
{

    public function store(Request $request){

           $validate = Validator::make($request->all(), [
               'number'     => 'required|digits:11|regex:/^0/|unique:users,number,id',
           ],[
            'number.digits' => 'Phone number must be 11 digits',
            'number.regex'  => 'Phone number must start with 0',
            // 'number.unique' => 'Phone number already exists, Please login',
           ]);

           // if validation fails
           if ($validate->fails()) {
                return response()->json([
                    'status'    => 0,
                    'message'   => $validate->errors()->messages(),
                ],200);
           }

           $patient = User::where('number', $request->number)->where('role','patient')->first();
           //patient check or create
           if($patient){
            //patient register check
            if($patient->register_at != null){
                return response()->json([
                    'status'    => 1,
                    'message'   => "User already registerd, Please login",
                ],200);
            }else{
                return response()->json([
                    'status'    => 1,
                    'message'   => "Number not verified, Please verify your account via OTP",
                    'data'      => $patient
                ],200);
            }

           }else{
               try {
                $user = User::create([
                    // 'name'      => $request->name,
                    // 'email'     => $request->email,
                    'number'    => $request->number,
                    // 'password'  => Hash::make($request->password),
                    'role'      => 'patient'
                ]);
              $otp =  OtpVerify::create([
                   'type' => 'patient',
                   'user_id' => $user->id,
                   'otp' => 1234,
                //    'otp' => rand(1000, 9999),
                   'count' => 0,
                   'duration' => now()->addMinutes(3),
               ]);
            //    SmsOtp::Send($request->number, 'Your Facebook password reset successfully!');
                return response()->json([
                    'status' => 1,
                    'message'   => "OTP sent successfully, Expire in 3 minutes",
                    'data'   => $user,

                    // 'token' =>$user->createToken('passportToken')->accessToken
                ],200);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 0,
                    'message'   => "Failed to create user, $th",
                ],200);
            }
           }

    }
    public function otp(Request $request){
        $validate = Validator::make($request->all(), [
            'user_id'     => 'required',
            'otp'         => 'required',
        ],[
            'user_id.required' => 'User ID is required',
            'otp.required' => 'OTP is required',
        ]);

        // if validation fails
        if ($validate->fails()) {
             return response()->json([
                 'status'    => 0,
                 'message'   => $validate->errors()->messages(),
             ],200);
        }
        $otp = OtpVerify::where('type', 'patient')->where('user_id', $request->user_id)->first();
        //check user
        if($otp == null ){
            return response()->json([
                'status'    => 0,
                'message'   => "User not found!",
            ],200);
        }else{
            //check otp
            if($otp->otp != $request->otp){
                //check otp count
                if($otp->count >= 3){
                    return response()->json([
                        'status' => 0,
                        'message'   => "OTP max count reached",
                        'attempt'   => $otp->count
                    ],200);
                }
                $otp->count = $otp->count + 1;
                $otp->save();
                return response()->json([
                    'status'    => 0,
                    'message'   => "OTP not matched!",
                    'attempt'   => $otp->count
                ],200);
            }else{
                //check otp expriy
                if($otp->duration < now()){
                    return response()->json([
                        'status' => 0,
                        'message'   => "OTP expired!",
                    ],200);
                }else{
                    $user = User::find($otp->user_id);
                    $user->register_at = now();
                    $user->save();
                    $patient = new Patient;
                    $patient->user_id = $user->id;
                    $patient->save();
                    $otp->delete();
                    return response()->json([
                        'status' => 1,
                        'message'   => "User registerd successfully",
                        'data'      => $user,
                        'token'     => $user->createToken('passportToken')->accessToken
                    ] ,200);
                }
            }
        }

    }
    public function resend_otp(Request $request){
        $validate = Validator::make($request->all(),[
            'user_id'    => 'required',
        ],[
            'user_id.required' => 'User ID is required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }
        $otp = OtpVerify::where('type', 'patient')->where('user_id', $request->user_id)->first();
        if($otp == null ){
            return response()->json([
                'status' => 0,
                'message'   => "User not found!",
            ],200);
        }else{
            // $otp->otp = rand(1000, 9999);
            $otp->otp = 1234;
            $otp->count = 0;
            $otp->duration = now()->addMinutes(3);
            $otp->save();
            // SmsOtp::Send($otp->user->number, 'Your OTP for registration is '.$otp->otp.'. Expire in 3 minutes.');
            return response()->json([
                'status' => 1,
                'message'   => "OTP sent successfully, Expire in 3 minutes",
                // 'data'   => $otp
            ],200);
        }

    }
}
