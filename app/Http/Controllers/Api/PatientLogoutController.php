<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PatientLogoutController extends Controller
{
    public function logout(Request $request)
    {


        // Get the user's token
        $token = auth('api')->user()->token();

        // Revoke the token
        $token->revoke();

        return response()->json([
            'status'    => 1,
            'message'   => 'Logged out successfully'
        ], 200);
    }
}
