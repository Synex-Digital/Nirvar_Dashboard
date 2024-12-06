<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function getNotifications(){
        $patient = Auth::guard('api')->user();
        if(!$patient){
            return response()->json([
                'status' => 0,
                'message' => "Patient not found",
            ], 200);
        }
        $notifications = Notification::where('notifiable_id', $patient->id)->get();
        return response()->json([
            'status' => 1,
            'message' => "success",
            'data' => $notifications,
        ], 200);
    }
}
