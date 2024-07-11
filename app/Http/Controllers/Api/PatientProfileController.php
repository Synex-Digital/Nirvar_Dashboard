<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            $data = $user->patient->weight_height;

            // Initialize variables
            $weight = null;
            $height = null;

            // Check for weight and height pattern
            if($data){
                if (preg_match('/^(\d+),?\s*(\d+)\s*FT\.?\s*(\d*)\s*IN?$/i', $data, $matches)) {
                    $weight = !empty($matches[1]) ? $matches[1] : null;
                    $height = trim($matches[2] . ' FT ' . $matches[3] . ' IN');
                } elseif (preg_match('/^(\d+),?\s*(\d+)\s*FT$/i', $data, $matches)) {
                    $weight = !empty($matches[1]) ? $matches[1] : null;
                    $height = trim($matches[2] . ' FT');
                } elseif (preg_match('/^(\d+)\s*FT\.?\s*(\d*)\s*IN?$/i', $data, $matches)) {
                    $weight = null;
                    $height = trim($matches[1] . ' FT ' . $matches[2] . ' IN');
                } elseif (preg_match('/^(\d+)$/i', $data, $matches)) {
                    $weight = $matches[1];
                    $height = null;
                } elseif (preg_match('/^(\d+)\s*FT$/i', $data, $matches)) {
                    $weight = null;
                    $height = trim($matches[1] . ' FT');
                } elseif (preg_match('/^(\d+)\s*FT\.?\s*(\d+)\s*IN?$/i', $data, $matches)) {
                    $weight = null;
                    $height = trim($matches[1] . ' FT ' . $matches[2] . ' IN');
                }
            }

            return response()->json([
                'status'        => 0,
                'message'       => "User found",
                'name'          => $user->name,
                'email'         => $user->email,
                'number'        => $user->number,
                'photo'         => $user->photo,
                'blood_gorup'   => $user->patient->blood_group,
                'date_of_birth' => $user->patient->date_of_birth,
                'age'           => $user->patient->date_of_birth ? \Carbon\Carbon::parse($user->patient->date_of_birth)->age : null,
                'weight'        => $weight,
                'height'        => $height,
                'gender'        => $user->patient->gender,
                'address'       => $user->patient->address,

            ],200);
            // return response()->json($user);
        }
    }

    public function profile_update(Request $request){
        $user = User::where('id',$request->user_id)->where('role','patient')->first();
        if(is_null($user)){
            return response()->json([
                'status'    => 0,
                'message'   => "User not found",
            ],200);
        }else{
            $request->validate([
                'password' => 'required|min:6'
            ]);
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->password = $request->password ? Hash::make($request->password): null;
            $user->save();
            $patient = $user->patient;
            $patient->blood_group   = $request->blood_group;
            $patient->date_of_birth = $request->date_of_birth;
            $patient->gender        = $request->gender;
            $patient->address       = $request->address;

            //weight and height
            $weight_height = null;
            $w = $request->weight ? $request->weight : null;
            $h = ($request->height_ft ? $request->height_ft . '.' : '') . ($request->height_in ? $request->height_in : '');
            if ($request->height_ft) {
                if ($request->height_in) {
                    $h = $request->height_ft . '.' . $request->height_in;
                } else {
                    $h = $request->height_ft;
                }
            } elseif ($request->height_in) {
                $h = $request->height_in;
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
            $patient->save();
            return response()->json([
                'status'    => 1,
                'message'   => "Profile updated successfully",
            ],200);
        }

    }

}
