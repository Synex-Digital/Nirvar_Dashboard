<?php

namespace App\Http\Controllers\Api;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PatientFileController extends Controller
{
    public function getFiles($id){
        $patient = Auth::guard('api')->user();
        $folder = Folder::find($id);
        if($folder == null){
            return response()->json([
                'status'    => 0,
                'message'   => "Folder not found",
            ], 200);
        }else{
            if($patient->id == $folder->user_id){
                return response()->json([
                    'status'    => 1,
                    'message'   => "success",
                    'files'     => $folder->files,
                ],200);
            }else{
                return response()->json([
                    'status'    => 0,
                    'message'   => "Folder access denied",
                ], 200);
            }
        }

    }


//upload
    public function upload(Request $request){

        $folder = Folder::find($request->folder_id);
        if(is_null($folder)){
            return response()->json([
                'status'    => 0,
                'message'   => "Folder not found to upload",
            ], 200);
        }else{
            if($folder->user_id == Auth::guard('api')->user()->id){
                $request->validate([
                    'file' => 'required|file|max:5124|mimes:pdf,jpeg,png,jpg,gif,heic',
                ]);

                $uploaded_file = $request->file('file');

                $info = pathinfo( $request->file_name);
                $filename = $info['filename'] .($request->type == 'prescription' ? 'PR-':'TR-'). rand(1000, 9999) . '.' . $info['extension'];
                $uploaded_file->move(public_path('uploads/patient/files'), $filename);
                $file = new File;
                $file->name = $filename;
                $file->folder_id = $request->folder_id;
                $file->save();

                return response()->json([
                    'status'    => 1,
                    'message'   => "File uploaded successfully",
                    'file'      => $file
                ], 200);
            }else{
                return response()->json([
                    'status'    => 0,
                    'message'   => "Folder access denied",
                ], 200);
            }
        }
    }

//delete
    public function delete($id){
        $file = File::find($id);
        if(is_null($file)){
            return response()->json([
                'status'    => 0,
                'message'   => "File not found",
            ], 200);
        }else{
            $path = public_path('uploads/patient/files/' . $file->name);
            unlink($path);
            $file->delete();
            return response()->json([
                'status'    => 1,
                'message'   => "File deleted successfully",
            ], 200);
        }
    }







}
