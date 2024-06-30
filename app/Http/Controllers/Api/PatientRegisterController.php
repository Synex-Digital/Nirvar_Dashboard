<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OtpVerify;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PatientRegisterController extends Controller
{

    public function store(Request $request){

            // Validate the request
           $validate = Validator::make($request->all(), [
               'name'       => 'required|string|max:255',
               'email'      => 'required|string|email|max:255|unique:users',
               'number'     => 'required|digits:11|regex:/^0/|unique:users',
               'password'   => 'required|string|min:8',
           ],[
            'number.digits' => 'Phone number must be 11 digits',
            'number.regex'  => 'Phone number must start with 0',
            'number.unique' => 'Phone number already exists',
           ]);

           // if validation fails
           if ($validate->fails()) {
                return response()->json([
                    'status'    => 0,
                    'message'   => $validate->errors()->messages(),
                ],200);
           }

            // Create the user
            try {
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->email,
                    'number'    => $request->number,
                    'password'  => Hash::make($request->password),
                    'role'      => 'patient',
                ]);
              $otp =  OtpVerify::create([
                   'type' => 'patient',
                   'user_id' => $user->id,
                   'otp' => rand(1000, 9999),
                   'count' => 0,
                   'duration' => now()->addMinutes(15),
               ]);
                return response()->json([
                    'status' => 1,
                    'message'   => "OTP sent successfully, Expire in 15 minutes",
                    'data'   => $user
                ],200);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 0,
                    'message'   => "Failed to create user, $th",
                ],200);
            }
    }
    public function otp(Request $request){
        $otp = OtpVerify::where('type', 'patient')->where('user_id', $request->user_id)->first();
        //check user
        if($otp == null ){
            return response()->json([
                'status' => 0,
                'message'   => "User not found!",
            ],200);
        }else{
            //check otp
            if($otp->otp != $request->otp){
                return response()->json([
                    'status' => 0,
                    'message'   => "OTP not matched!",

                ],200);
            }else{
                //check otp expriy
                if($otp->duration < now()){
                    return response()->json([
                        'status' => 0,
                        'message'   => "OTP expired!",
                    ]);
                }else{
                    $user = User::find($otp->user_id);
                    $user->register_at = now();
                    $user->save();
                    $otp->delete();
                    return response()->json($user ,200);
                }
            }
        }

    }
}
