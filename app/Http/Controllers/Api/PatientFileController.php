<?php

namespace App\Http\Controllers\Api;

use App\Models\File;
use Aws\S3\S3Client;
use App\Models\Folder;
use Illuminate\Http\Request;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PatientFileController extends Controller
{
    public function getFiles($id)
    {
        $patient = Auth::guard('api')->user();
        $folder = Folder::find($id);

        if ($folder == null) {
            return response()->json([
                'status' => 0,
                'message' => "Folder not found",
            ], 200);
        } else {
            if ($patient->id == $folder->user_id) {
                if ($folder->files->isEmpty()) {
                    return response()->json([
                        'status' => 1,
                        'message' => "No files found",
                    ]);
                } else {
                    return response()->json([
                        'status' => 1,
                        'message' => "success",
                        'data' => [
                            'prescription' => $folder->prescription_files->map(function ($file) {
                                $rename = $this->splitFileName($file->name);
                                $renameName = $rename ? $rename['name'] : null;
                                // Generate S3 URL for the file
                                $fileUrl = Storage::disk('s3')->url('uploads/patient/files/' . $file->name);
                                return [
                                    'folder_id' => $file->folder_id,
                                    'folder_name' => $file->folder->name,
                                    'id' => $file->id,
                                    'name' => $file->name,
                                    'rename' => $renameName,
                                    'type' => $file->type,
                                    'path' => $fileUrl,  // Return the S3 URL for the file
                                    'created_at' => $file->created_at->format('d-M-y'),
                                ];
                            }),
                            'test_report' => $folder->test_report_files->map(function ($file) {
                                $rename = $this->splitFileName($file->name);
                                $renameName = $rename ? $rename['name'] : null;
                                // Generate S3 URL for the file
                                $fileUrl = Storage::disk('s3')->url('uploads/patient/files/' . $file->name);
                                return [
                                    'folder_id' => $file->folder_id,
                                    'folder_name' => $file->folder->name,
                                    'id' => $file->id,
                                    'name' => $file->name,
                                    'rename' => $renameName,
                                    'type' => $file->type,
                                    'path' => $fileUrl,  // Return the S3 URL for the file
                                    'created_at' => $file->created_at->format('d-M-y'),
                                ];
                            })
                        ]
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => "Folder access denied",
                ], 200);
            }
        }
    }

    // public function getFiles($id)
    // {

    //     $patient = Auth::guard('api')->user();
    //     $folder = Folder::find($id);
    //     if ($folder == null) {
    //         return response()->json([
    //             'status'    => 0,
    //             'message'   => "Folder not found",
    //         ], 200);
    //     } else {
    //         if ($patient->id == $folder->user_id) {
    //             if ($folder->files->isEmpty()) {
    //                 return response()->json([
    //                     'status'    => 1,
    //                     'message'   => "No files found",
    //                 ]);
    //             } else {
    //                 return response()->json([
    //                     'status'    => 1,
    //                     'message'   => "success",
    //                     'data'      => [
    //                         'prescription' => $folder->prescription_files->map(function ($file) {
    //                             $rename = $this->splitFileName($file->name);
    //                             $renameName = $rename ? $rename['name'] : null;
    //                             return [
    //                                 'folder_id'     => $file->folder_id,
    //                                 'folder_name'   => $file->folder->name,
    //                                 'id'            => $file->id,
    //                                 'name'          => $file->name,
    //                                 'rename'        => $renameName,
    //                                 'type'          => $file->type,
    //                                 'path'          => url('uploads/patient/files/' . $file->name),
    //                                 'created_at'    => $file->created_at->format('d-M-y'),
    //                             ];
    //                         }),
    //                         'test_report' => $folder->test_report_files->map(function ($file) {
    //                             $rename = $this->splitFileName($file->name);
    //                             $renameName = $rename ? $rename['name'] : null;
    //                             return [
    //                                 'folder_id'     => $file->folder_id,
    //                                 'folder_name'   => $file->folder->name,
    //                                 'id'            => $file->id,
    //                                 'name'          => $file->name,
    //                                 'rename'        => $renameName,
    //                                 'type'          => $file->type,
    //                                 'path'          => url('uploads/patient/files/' . $file->name),
    //                                 'created_at'    => $file->created_at->format('d-M-y'),
    //                             ];
    //                         })

    //                     ]
    //                 ], 200);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status'    => 0,
    //                 'message'   => "Folder access denied",
    //             ], 200);
    //         }
    //     }
    // }


    //upload
    public function upload(Request $request)
    {
        $file = $request->file('file');
        Log::info("File Info:  $request->file('file') ");
        Log::info('Uploaded File Size: ' . $file->getSize() . ' bytes');
        Log::info('Upload Error Code: ' . $file->getError());
        $validate = Validator::make($request->all(), [
            'folder_id'     => 'required',
            'file'          => 'required|file',
            'file_name'     => 'required',
        ], [
            'name.required' => 'Folder name is required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ], 200);
        }
        $folder = Folder::find($request->folder_id);
        if (is_null($folder)) {
            return response()->json([
                'status'    => 0,
                'message'   => "Folder not found to upload",
            ], 200);
        } else {
            if ($folder->user_id == Auth::guard('api')->user()->id) {
                $explodedName = explode('.', $request->file_name);
                $name = implode('.', array_slice($explodedName, 0, -1));
                $extension = end($explodedName);
                $uploaded_file = $request->file('file');
                $filename = $name . ($request->type == 'prescription' ? '_PR-' : '_TR-') . rand(1000, 9999) . '.' . $extension;

                // Store the file on S3
                $path = $uploaded_file->storeAs('uploads/patient/files', $filename, 's3');

                // Create a new file record in the database
                $file = new File;
                $file->name = $filename;
                $file->folder_id = $request->folder_id;
                $file->type = $request->type;
                $file->save();

                // Return the response with the file URL on S3
                return response()->json([
                    'status' => 1,
                    'message' => 'File uploaded successfully',
                    'data' => $file,
                    'file_url' => Storage::disk('s3')->url($path),  // Get the file URL from S3
                ], 200);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => "Folder access denied",
                ], 200);
            }
        }
    }

    //delete
    public function delete($id)
    {
        $file = File::find($id);

        if (is_null($file)) {
            return response()->json([
                'status'    => 0,
                'message'   => "File not found",
            ], 200);
        } else {
            $path = 'uploads/patient/files/' . $file->name;

            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
                $file->delete();

                return response()->json([
                    'status'    => 1,
                    'message'   => "File deleted successfully from S3",
                ], 200);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => "File not found on S3",
                ], 200);
            }
        }
    }


    // public function delete($id)
    // {
    //     $file = File::find($id);
    //     if (is_null($file)) {
    //         return response()->json([
    //             'status'    => 0,
    //             'message'   => "File not found",
    //         ], 200);
    //     } else {
    //         $path = public_path('uploads/patient/files/' . $file->name);
    //         unlink($path);
    //         $file->delete();
    //         return response()->json([
    //             'status'    => 1,
    //             'message'   => "File deleted successfully",
    //         ], 200);
    //     }
    // }

    public function rename(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'folder_id'     => 'required',
            'file_id'       => 'required',
            'file_type'     => 'required',
            'file_name'     => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ]);
        }

        $file = File::where('folder_id', $request->folder_id)
            ->where('id', $request->file_id)
            ->where('type', $request->file_type)
            ->first();

        if (is_null($file)) {
            return response()->json([
                'status'    => 0,
                'message'   => "File not found",
            ], 200);
        } else {
            $originalName = $file->name;
            $newName = trim($request->file_name);

            $fileParts = $this->splitFileName($originalName);

            if (!$fileParts) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Invalid file name format'
                ], 200);
            }

            $newFileName = $newName . $fileParts['code'] . $fileParts['extension'];

            $originalPath = 'uploads/patient/files/' . $originalName;
            $newPath = 'uploads/patient/files/' . $newFileName;
            if (Storage::disk('s3')->exists($originalPath)) {
                Storage::disk('s3')->move($originalPath, $newPath);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'File not found to rename on S3'
                ], 200);
            }

            $file->name = $newFileName;
            $file->save();

            return response()->json([
                'status'    => 1,
                'message'   => "File renamed successfully",
                'data'      => $file
            ], 200);
        }
    }


    // public function rename(Request $request)
    // {
    //     $validate = Validator::make($request->all(), [
    //         'folder_id'     => 'required',
    //         'file_id'       => 'required',
    //         'file_type'     => 'required',
    //         'file_name'     => 'required',
    //     ]);
    //     if ($validate->fails()) {
    //         return response()->json([
    //             'status'    => 0,
    //             'message'   => $validate->errors()->messages(),
    //         ]);
    //     }

    //     $file = File::where('folder_id', $request->folder_id)->where('id', $request->file_id)->where('type', $request->file_type)->first();
    //     if (is_null($file)) {
    //         return response()->json([
    //             'status'    => 0,
    //             'message'   => "File not found",
    //         ], 200);
    //     } else {
    //         $originalName = $file->name;
    //         $newName = trim($request->file_name);
    //         $fileParts = $this->splitFileName($originalName);
    //         if ($fileParts) {
    //             $newFileName = $newName . $fileParts['code'] . $fileParts['extension'];
    //             $path = public_path('uploads/patient/files/' . $originalName);
    //             $new_path = public_path('uploads/patient/files/' . $newFileName);
    //             if (file_exists($path)) {
    //                 rename($path, $new_path);
    //             } else {
    //                 return response()->json([
    //                     'status' => 0,
    //                     'message' => 'File not found to rename'
    //                 ], 200);
    //             }
    //             $file->name = $newFileName;
    //             $file->save();
    //         } else {
    //             return response()->json([
    //                 'status' => 0,
    //                 'message' => 'Invalid file name format'
    //             ], 200);
    //         }

    //         return response()->json([
    //             'status'    => 1,
    //             'message'   => "File renamed successfully",
    //             'data'      => $file
    //         ], 200);
    //     }
    // }
    function splitFileName($filename)
    {
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


    public function download($id)
    {
        $file = File::find($id);

        // If file not found, return error response
        if (is_null($file)) {
            return response()->json([
                'status'    => 0,
                'message'   => "File not found",
            ], 200);
        } else {
            // Construct the S3 path for the file
            $path = 'uploads/patient/files/' . $file->name;
            if (Storage::disk('s3')->exists($path)) {
                $url = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5));

                return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'data'   => $url
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'File not found on S3'
                ], 200);
            }
        }
    }


    public function latest()
    {
        $patient = Auth::guard('api')->user();
        $folders = $patient->folders;
        $files = File::whereIn('folder_id', $folders->pluck('id'))->latest()->get()->take(2);

        if ($files->isEmpty()) {
            return response()->json([
                'status'    => 0,
                'message'   => "No file is uploaded recently",
            ]);
        } else {
            return response()->json([
                'status'    => 1,
                'message'   => "success",
                'data'      =>
                $files->map(function ($file) {
                    $rename = $this->splitFileName($file->name);
                    $renameName = $rename ? $rename['name'] : null;

                    // Generate the S3 path for the file
                    $path = 'uploads/patient/files/' . $file->name;

                    // Check if the file exists on S3 and generate the presigned URL for download
                    $url = Storage::disk('s3')->url($path);

                    return [
                        'folder_id'     => $file->folder_id,
                        'folder_name'   => $file->folder->name,
                        'id'            => $file->id,
                        'name'          => $file->name,
                        'rename'        => $renameName,
                        'type'          => $file->type,
                        'path'          => $url,  // Use the S3 URL for the file
                        'created_at'    => $file->created_at->diffForHumans(),
                    ];
                })

            ]);
        }
    }
}
