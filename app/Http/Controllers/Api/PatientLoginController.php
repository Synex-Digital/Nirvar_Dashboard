<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PatientLoginController extends Controller
{

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'number'     => 'required|digits:11|regex:/^0/',
            'password'   => 'required|min:8',
        ],[
            'number.digits' => 'Phone number must be 11 digits',
            'number.regex'  => 'Phone number must start with 0',
        ]);

        // if validation fails
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }
        $user = User::where('number', $request->number)->where('role','patient')->first();

        if (!$user || $user->register_at == null || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Invalid Credentials',
            ],200);
        }elseif($user->register_at|| Hash::check($request->password, $user->password)){
            return response()->json([
                'status'    => 1,
                'message'   => 'Login successfully',
                'data'      => $user,
                'token'     => $user->createToken('passportToken')->accessToken

            ],200);
        }

    }

}
