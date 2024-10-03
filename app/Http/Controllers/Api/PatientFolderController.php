<?php

namespace App\Http\Controllers\Api;

use App\Models\Folder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PatientFolderController extends Controller
{

    public function selectFolders(){
        $folders = Auth::guard('api')->user()->folders;
        if(count($folders) > 0){
            return response()->json([
                'status'    => 1,
                'data'      => $folders->map(function ($folder) {
                    return [
                        'id'            => $folder->id,
                        'name'          => $folder->name,

                    ];
                }),
            ], 200);
        }else{
            return response()->json([
                'status'    => 0,
                'message'   => "No folders found",
            ], 200);
        }

    }
    public function getFolders(){
        $folders = Auth::guard('api')->user()->folders;
        if(count($folders) > 0){
            return response()->json([
                'status'    => 1,
                'data'      => $folders->map(function ($folder) {
                    return [
                        'id'            => $folder->id,
                        'user_id'       => $folder->user_id,
                        'name'          => $folder->name,
                        'file_count'    => count($folder->files),
                        'created_at'    => $folder->created_at->format('d-M-y'),
                    ];
                }),
            ], 200);
        }else{
            return response()->json([
                'status'    => 0,
                'message'   => "No folders found",
            ], 200);
        }

    }

    public function create(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name'    => 'required',
        ],[
            'name.required' => 'Folder name is required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Folder name is required',
            ],200);
        }
        $user = Auth::guard('api')->user()->id;
        $folder_database = Folder::where('user_id', $user)->where('name', $request->name)->first();

        if(is_null($folder_database)){
            $folder = new Folder;
            $folder->name = $request->name;
            $folder->user_id = $user;
            $folder->save();
            return response()->json([
                'status'    => 1,
                'message'   => "Folder created successfully",
                'data'      => $folder
            ], 200);
        }else{
            return response()->json([
                'status'    => 0,
                'message'   => "Folder name already exists",
            ], 200);
        }
    }

    public function update(Request $request){
        $validate = Validator::make($request->all(),[
            'folder_id'     => 'required',
            'name'          => 'required',
        ],[
            'name.required' => 'Folder name cannot be empty',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }
        $folder = Folder::find($request->folder_id);
        if(is_null($folder)){
            return response()->json([
                'status'    => 0,
                'message'   => "Folder not found",
            ], 200);
        }else{
            if(Folder::where('user_id', $folder->user_id)->where('name', $request->name)->first()){
                return response()->json([
                    'status'    => 0,
                    'message'   => "Folder name already exists",
                ], 200);
            }else{
                $folder->name = $request->name;
                $folder->save();
                return response()->json([
                    'status'    => 1,
                    'message'   => "Folder updated successfully",
                    'data'      => $folder
                ], 200);
            }

        }
    }

    public function delete($id){
        $folder = Folder::find($id);
        if(is_null($folder)){
            return response()->json([
                'status'    => 0,
                'message'   => "Folder not found",
            ], 200);
        }else{
            foreach($folder->files as $data){
                $path = public_path('uploads/patient/files/' . $data->name);
                unlink($path);
            }
            $folder->files()->delete();
            $folder->delete();
            return response()->json([
                'status'    => 1,
                'message'   => "Folder and files in it deleted successfully",
            ], 200);
        }
    }


}
