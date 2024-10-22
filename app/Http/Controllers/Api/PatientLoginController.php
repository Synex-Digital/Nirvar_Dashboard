<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\NotificationToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PatientLoginController extends Controller
{

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'number'     => 'required|digits:11|regex:/^0/',
            'password'   => 'required|min:6',
        ],[
            'number.required' => 'Phone number is required',
            'number.digits' => 'Phone number must be 11 digits',
            'number.regex'  => 'Phone number must start with 0',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ]);

        // if validation fails
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }
        $user = User::where('number', $request->number)->where('role','patient')->first();
        if($user){
            if($user->register_at == null){
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Please verify your account via OTP',
                ],200);
            }elseif($user->password == null){
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Please verify and update your profile',
                    'user_id'   => $user->id
                ],200);

            }elseif(Hash::check($request->password, $user->password)){
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Login successfully',
                    'data'      => $user->map(function ($user) {
                       return[
                            'id'        => $user->id,
                            'name'      => $user->name,
                            'email'     => $user->email,
                            'number'    => $user->number,
                            'register_at' => $user->register_at,
                            'photo'     => url('uploads/patient/profile/'.$user->photo),
                            'role'      => $user->role,
                            'email_verified_at' => $user->email_verified_at,
                            'ceated_at' => $user->created_at,
                            'updated_at' => $user->updated_at,
                       ];
                    }),
                    'token'     => $user->createToken('passportToken')->accessToken
                ],200);
            }else{
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Invalid Credentials ',
                ],200);
            }
        }else{
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid Credentials ',
            ],200);
        }

        // if (!$user || $user->register_at == null || !Hash::check($request->password, $user->password)) {
        //     return response()->json([
        //         'status'    => 0,
        //         'message'   => 'Invalid Credentials',
        //     ],200);
        // }elseif($user->register_at|| Hash::check($request->password, $user->password)){
        //     return response()->json([
        //         'status'    => 1,
        //         'message'   => 'Login successfully',
        //         'data'      => $user,
        //         'token'     => $user->createToken('passportToken')->accessToken

        //     ],200);
        // }

    }
    public function get_fcm_token(Request $request){
        $validate = Validator::make($request->all(), [
            'user_id'     => 'required',
            'device_token'   => 'required',
            'device_id'   => 'required',
            'device_type'   => 'required',
        ],[
            'user_id' => 'user ID is required',
            'device_token'  => 'FCM token is required',
            'device_id'  => 'device id is required',
            'device_type'  => 'device type is required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }

        $user = User::find($request->user_id);
        if($user){
            $notification_token = new NotificationToken();
            $notification_token->user_id = $request->user_id;
            $notification_token->device_token = $request->device_token;
            $notification_token->device_id = $request->device_id;
            $notification_token->device_type = $request->device_type;
            $notification_token->save();
            return response()->json([
                'status'    => 1,
                'message'   => 'token added success',
            ],200);
        }else{
            return response()->json([
                'status'    => 0,
                'message'   => 'user not found',
            ],200);
        }

    }

}
