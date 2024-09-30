<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialist;
use Illuminate\Http\Request;

class SpecialityController extends Controller
{
    public function specialities(){
        $specialities = Specialist::all();
        if(!$specialities){
            return response()->json([
                'status' => 0,
                'message' => "No data found",

            ]);
        }
        return response()->json([
            'status' => 1,
            'message' => "success",
            'data' =>
                $specialities->map(function ($speciality){
                    return [
                        'id' => $speciality->id,
                        'name' => $speciality->name,
                    ];
                }),
        ]);

    }
}
