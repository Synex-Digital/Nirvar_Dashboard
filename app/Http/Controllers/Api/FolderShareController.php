<?php

namespace App\Http\Controllers\Api;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;

class FolderShareController extends Controller
{
    public function share($folderId)
    {
        $folder = Folder::findOrFail($folderId);
        if(auth()->user()->id != $folder->user_id){
            return response()->json([
                'status'    => 0,
                'message'   => 'Folder not found',
            ]);
        }

        $url = URL::temporarySignedRoute(
            'folder.access', now()->addMinutes(10), ['folder' => $folder->id]
        );
        return response()->json([
            'status' => 1,
            'message' => 'Folder shared successfully',
            'url' => $url,
        ]);
    }

    public function access(Request $request, $folderId)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $folder = Folder::with('prescription_files', 'test_report_files', 'files')->findOrFail($folderId);
        return view('folderShare', compact('folder'));
    }

    public function generate(Request $request, $filename)
    {
        $url = URL::temporarySignedRoute(
            'image.access', now()->addMinutes(10), ['filename' => $filename]
        );

        return response()->json(['url' => $url]);
    }
    public function imageAccess(Request $request, $filename)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $path = public_path("uploads/patient/files/{$filename}");

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function generateImage(Request $request, $fileId)
    {
        // Ensure the user is authenticated or add other checks as necessary
        $url = URL::temporarySignedRoute(
            'image.show', now()->addMinutes(10), ['fileId' => $fileId]
        );

        return response()->json(['url' => $url]);
    }

    // Method to serve the image if accessed via a valid signed URL
    public function show(Request $request, $fileId)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }
        $file = File::find($fileId);

        if (!$file) {
            abort(404);
        }

        $path = public_path('/uploads/patient/files/' . $file->name);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
