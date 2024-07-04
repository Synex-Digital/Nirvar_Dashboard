<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;

class PatientFolderController extends Controller
{

    public function getFolders($id){
        $folders = Folder::where('user_id', $id)->get();
        if(count($folders) > 0){
            return response()->json([
                'status'    => 1,
                'data'      => $folders,
            ]);
        }else{
            return response()->json([
                'status'    => 0,
                'message'   => "No folders found",
            ], 200);
        }

    }

    public function create(Request $request)
    {
        $user = $request->user_id;
        $folder_database = Folder::where('name', $request->name)->first();

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
        $folder = Folder::find($request->folder_id);
        if(is_null($folder)){
            return response()->json([
                'status'    => 0,
                'message'   => "Folder not found",
            ], 200);
        }else{
            if(Folder::where('name', $request->name)->first()){
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
            $folder->delete();
            return response()->json([
                'status'    => 1,
                'message'   => "Folder deleted successfully",
            ], 200);
        }
    }


}
