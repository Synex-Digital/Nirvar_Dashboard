<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PatientFolderController extends Controller
{

    public function create(Request $request)
    {
        $user = $request->user_id;
        $folder = $request->name;
        $path = "patients/{$user}/{$folder}";

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        return response()->json(['path' => $path], 200);
        // $request->validate([
        //     'patient_id' => 'required|exists:users,id',
        //     'name' => 'required',
        // ]);
        // $user = auth()->user();
        // $folder = $user->folders()->create([
        //     'name' => $request->name
        // ]);
        // return response()->json($folder);
    }

    public function getFolders($id)
    {
        $user = $id;
        $directories = Storage::directories("patients/{$user}");
 // Extract only the folder names
 $folderNames = array_map(function($dir) {
    return basename($dir);
}, $directories);

return response()->json(['folders' => $folderNames], 200);
    }
}
