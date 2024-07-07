<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PatientProfileController extends Controller
{
    public function profile($id){
        $user = User::where('id',$id)->where('role','patient')->first();
        if(is_null($user)){
            return response()->json([
                'status'    => 0,
                'message'   => "User not found",
            ],200);
        }else{
            return response()->json([
                'status'    => 0,
                'message'   => "User found",
                'user'      => Auth::user(),
                'patient'   => $user->patient,
            ],200);
            // return response()->json($user);
        }
    }

    public function profile_update(Request $request){
        return response()->json($request->all());
    }

}
