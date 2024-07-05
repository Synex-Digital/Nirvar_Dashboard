<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PatientLogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Get the user's token
        $token = $request->user()->token();

        // Revoke the token
        $token->revoke();

        return response()->json([
            'status'    => 1,
            'message'   => 'Logged out successfully'
        ], 200);
    }
}
