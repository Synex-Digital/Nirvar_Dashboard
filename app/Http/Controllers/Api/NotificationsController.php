<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
    public function readNotification(Request $request){
        $validate = Validator::make($request->all(),[
            'notification_id'     => 'required',
        ]);
        if($validate->fails()){
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }
        $notification = Notification::find($request->notification_id);
        if(is_null($notification)){
            return response()->json([
                'status'    => 0,
                'message'   => "Notification not found",
            ], 200);
        }else{
            $notification->read_at = now();
            $notification->save();
            return response()->json([
                'status'    => 1,
                'message'   => "Notification marked as read",
            ], 200);
        }
    }
}
