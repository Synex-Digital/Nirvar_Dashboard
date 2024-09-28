<?php

namespace App\Http\Controllers\Api;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;

class FolderShareController extends Controller
{
    public function share($folderId)
    {

        $folder = Folder::findOrFail($folderId);

        $url = URL::temporarySignedRoute(
            'folders.access', now()->addMinutes(100), ['folder' => $folder->id]
        );

        return response()->json(['url' => $url]);
    }

    public function access(Request $request, $folderId)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $folder = Folder::with('files')->findOrFail($folderId);
        return view('folderShare', compact('folder'));
    }
}
