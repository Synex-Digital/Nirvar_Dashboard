<?php

namespace App\Http\Controllers\Api;

use App\Models\File;
use App\Models\User;
use App\Models\Folder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
                'status'        => 1,
                'message'       => "User found",
                'data'          => [
                    'name'          => $user->name,
                    'email'         => $user->email,
                    'number'        => $user->number,
                    'photo'         =>url('uploads/patient/profile/'.$user->photo) ,
                    'blood_gorup'   => $user->patient->blood_group,
                    'date_of_birth' => $user->patient->date_of_birth,
                    'age'           => $user->patient->date_of_birth ? \Carbon\Carbon::parse($user->patient->date_of_birth)->age : null,
                    'weight'        => $weight,
                    'height'        => $height,
                    'gender'        => $user->patient->gender,
                    'address'       => $user->patient->address,
                ]


            ],200);
            // return response()->json($user);
        }
    }

    public function profile_register(Request $request){
        $validate = Validator::make($request->all(),[
            'user_id'       => 'required',
            'name'          => 'required',
            'email'         => $request->email ? 'required|email|unique:users,email' : 'nullable',
            'password'      => 'required|min:6',
            'gender'        => 'required',
            'date_of_birth' => 'required',
            'blood_group'   => 'required',
            'photo'         => $request->photo ? 'mimes:jpg,jpeg,png,webp,heif' : 'nullable',
        ],[
            'user_id.required'          => 'User id is required',
            'name.required'             => 'Name is required',
            'email.required'            => 'Email is required',
            'email.email'               => 'Email must be a valid email address',
            'gender.required'           => 'Gender is required',
            'date_of_birth.required'    => 'Date of birth is required',
            'blood_group.required'      => 'Blood group is required',
            'password.required'         => 'Password is required',
            'password.min'              => 'Password must be at least 6 characters',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }
        $user = User::where('id',$request->user_id)->where('role','patient')->first();
        if(is_null($user)){
            return response()->json([
                'status'    => 0,
                'message'   => "User not found",
            ],200);
        }else{

            $user->name     = $request->name;
            $user->email    = $request->email?? null;
            $user->password = Hash::make($request->password);
            $uploaded_file  = $request->file('photo');
            $extn           = $uploaded_file?->getClientOriginalExtension();
            $fileName       = 'PROFILE_'.rand(100000,999999) . '.' . $extn;
            $uploaded_file?->move(public_path('uploads/patient/profile/'), $fileName);
            $user->photo    = $uploaded_file ?  $fileName : null;
            $user->save();
            $patient = $user->patient;
            $patient->blood_group   = $request->blood_group;
            $patient->date_of_birth = $request->date_of_birth;
            $patient->gender        = $request->gender;
            $patient->address       = $request->address ?? null;

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
                'number'   => $user->number,
            ],200);
        }

    }
    public function profile_update(Request $request){
        $user = User::find(auth()->user()->id);
        $validate = Validator::make($request->all(),[
            'photo'         => 'required|mimes:jpg,jpeg,png,webp,heif',
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email,'.$user->id,
            'gender'        => 'required',
            'date_of_birth' => 'required',
            'blood_group'   => 'required',
            'weight'        => 'required',
            'height_ft'     => 'required',
            'height_in'     => 'required',
            'address'       => 'required',

        ],[
            'name.required'             => 'Name is required',
            'email.required'            => 'Email is required',
            'email.email'               => 'Email must be a valid email address',
            'gender.required'           => 'Gender is required',
            'date_of_birth.required'    => 'Date of birth is required',
            'blood_group.required'      => 'Blood group is required',
            'weight.required'           => 'Weight is required',
            'height_ft.required'        => 'Height ft is required',
            'height_in.required'        => 'Height in is required',
            'address.required'          => 'Address is required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }

        if(is_null($user)){
            return response()->json([
                'status'    => 0,
                'message'   => "User not found",
            ],200);
        }else{
            $user->name     = $request->name;
            $user->email    = $request->email;
            if($user->photo !== null){
               $path = public_path('uploads/patient/profile/'.$user->photo);
               unlink($path);
            }
            $uploaded_file  = $request->file('photo');
            $extn           = $uploaded_file->getClientOriginalExtension();
            $fileName       = 'PROFILE_'.rand(100000,999999) . '.' . $extn;
            $uploaded_file->move(public_path('uploads/patient/profile/'), $fileName);
            $user->photo    =$fileName;
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
    public function search(Request $request){
        $validate = Validator::make($request->all(),[
            'search_data' => 'required',

        ],[
            'search_data.required' => 'Search input cannot be empty',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }
        $user_id = auth('api')->user()->id;
        $search_data = $request->search_data;

        // Fetch folders that belong to the user and match the search criteria
        $folders = Folder::where('user_id', $user_id)
            ->where('name', 'like', '%' . $search_data . '%')
            ->get();

        // Fetch files that belong to the user's folders and match the search criteria
        $files = File::whereHas('folder', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->where('name', 'like', '%' . $search_data . '%')
            ->get();

            // Prepare the response
            return response()->json([
                'status' => 1,
                'message' => "success",
                'data'    => [
                    'folders' => $folders->map(function ($folder) {
                        return [
                            'id'            => $folder->id,
                            'user_id'       => $folder->user_id,
                            'name'          => $folder->name,
                            'file_count'    => count($folder->files),
                            'created_at'    => $folder->created_at->format('d-M-y'),
                        ];
                    }),
                    'files' => $files->map(function ($file) {
                        $rename = $this->splitFileName($file->name);
                        $renameName = $rename ? $rename['name'] : null;
                        return [
                            'folder_id'     => $file->folder_id,
                            'folder_name'   => $file->folder->name,
                            'id'            => $file->id,
                            'name'          => $file->name,
                            'rename'        => $renameName,
                            'type'          => $file->type,
                            'path'          => url('uploads/patient/files/' . $file->name),
                            'created_at'    => $file->created_at->format('d-M-y'),
                        ];
                    }),
                ]

            ], 200);
    }
    public function password_change(Request $request){
        $validate = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
        ],[
            'old_password' => 'Old password is required',
            'new_password'  => 'New password is required',

        ]);
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }

        $user = User::find(auth('api')->user()->id);
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Old password is incorrect',
            ],200);
        }else{
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'status'    => 1,
                'message'   => 'Password changed successfully',
            ],200);
        }



    }
    function splitFileName($filename) {
        $pattern = '/(.+)(_PR-\d{4}|_TR-\d{4})(\.\w+)$/i'; // Regex to extract the base name and the code
        preg_match($pattern, $filename, $matches);

        if (!empty($matches)) {
            return [
                'name' => $matches[1], // Base name
                'code' => $matches[2], // PR-XXXX or TR-XXXX
                'extension' => $matches[3] // File extension
            ];
        }

        return null; // Return null if the pattern does not match
    }

}
