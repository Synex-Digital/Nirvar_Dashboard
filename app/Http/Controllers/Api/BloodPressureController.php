<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\BloodPressure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BloodPressureController extends Controller
{

    public function blood_pressure_today(){
        $user = auth('api')->user();
        $data = BloodPressure::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();
        if($data->count() == 0){
            return response()->json([
                'status'    => 0,
                'message'   => "No data found",
            ], 200);
        }else{
            return response()->json([
                'status'    => 1,
                'message'   => "success",
                'data'      => $data,
                'avg_systolic'   => ceil($data->avg('systolic') + 0.5),
                'avg_diastolic'  => ceil($data->avg('diastolic') + 0.5),
            ], 200);
        }

    }
    public function blood_pressure_seven_days(){
        $user = auth('api')->user();
        $data = BloodPressure::where('user_id', $user->id)
            ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->orderBy('created_at', 'desc')
            ->get();
        if($data->count() == 0){
            return response()->json([
                'status'    => 0,
                'message'   => "No data found",
            ], 200);
        }else{
            return response()->json([
                'status'    => 1,
                'message'   => "success",
                'data'      => $data,
            ], 200);
        }

    }
    public function blood_pressure_weekly(){
        $user = auth('api')->user();
        // Get the current date and the first day of the current month
        $currentDate = Carbon::now();
        $firstDayOfMonth = $currentDate->copy()->startOfMonth();

        // Define the start date of each week in the current month
        $weekOneStart = $firstDayOfMonth;
        $weekTwoStart = $firstDayOfMonth->copy()->addDays(7);
        $weekThreeStart = $firstDayOfMonth->copy()->addDays(14);
        $weekFourStart = $firstDayOfMonth->copy()->addDays(21);
        $weekFiveStart = $firstDayOfMonth->copy()->addDays(28);

        // Query to calculate weekly averages
        $weeklyAverages = [
            'Week One' => DB::table('blood_pressures')
                ->where('user_id', $user->id)
                ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
                ->whereBetween('created_at', [$weekOneStart, $weekTwoStart])
                ->first(),

            'Week Two' => DB::table('blood_pressures')
                ->where('user_id', $user->id)
                ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
                ->whereBetween('created_at', [$weekTwoStart, $weekThreeStart])
                ->first(),

            'Week Three' => DB::table('blood_pressures')
                ->where('user_id', $user->id)
                ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
                ->whereBetween('created_at', [$weekThreeStart, $weekFourStart])
                ->first(),

            'Week Four' => DB::table('blood_pressures')
                ->where('user_id', $user->id)
                ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
                ->whereBetween('created_at', [$weekFourStart, $weekFiveStart])
                ->first(),
        ];
        // Fetch data for the past 4 weeks
        return response()->json([
            'status'    => 1,
            'message'   => "success",
            'data'      => $weeklyAverages,
        ], 200);

    }
    public function blood_pressure_monthly(){
        $user = auth('api')->user();
       // Get the current date and define the start of the last four months
       $currentDate = Carbon::now();
       $monthOneStart = $currentDate->copy()->startOfMonth();
       $monthOneEnd = $currentDate->copy()->endOfMonth();

       $monthTwoStart = $currentDate->copy()->subMonth(1)->startOfMonth();
       $monthTwoEnd = $currentDate->copy()->subMonth(1)->endOfMonth();

       $monthThreeStart =$currentDate->copy()->subMonth(3)->endOfMonth();
       $monthThreeEnd = $currentDate->copy()->subMonth(1)->startOfMonth();


       $monthFourStart = $currentDate->copy()->subMonth(3)->startOfMonth();
       $monthFourEnd = $currentDate->copy()->subMonth(3)->endOfMonth();

        // Query to calculate monthly averages and round them
        $monthlyAverages = [
            'Month One' => DB::table('blood_pressures')
                ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
                ->whereBetween('created_at', [$monthOneStart, $monthOneEnd])
                ->first(),

            'Month Two' => DB::table('blood_pressures')
                ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
                ->whereBetween('created_at', [$monthTwoStart, $monthTwoEnd])
                ->first(),

            'Month Three' => DB::table('blood_pressures')
                ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
                ->whereBetween('created_at', [$monthThreeStart, $monthThreeEnd])
                ->first(),

            'Month Four' => DB::table('blood_pressures')
                ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
                ->whereBetween('created_at', [$monthFourStart, $monthFourEnd])
                ->first(),
        ];
        // Fetch data for the past 4 months
        return response()->json([
            'status'    => 1,
            'message'   => "success",
            'data'      => $monthlyAverages,
        ], 200);

    }


    public function blood_pressure_store(Request $request){
        $validate = Validator::make($request->all(), [
            'systolic' => 'required|integer|min:1',
            'diastolic' => 'required|integer|min:1',
        ]);
         // if validation fails
         if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }


        $submissionCount = BloodPressure::where('user_id', auth('api')->user()->id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();

        if ($submissionCount >= 2) {
            return response()->json(['status' => 0,'error' => 'You can only submit your blood pressure data twice within a 24-hour period.'] );
        }

        $bp = new Bloodpressure();
        $bp->user_id =auth('api')->user()->id;
        $bp->systolic = $request->systolic;
        $bp->diastolic = $request->diastolic;

        if ($bp->systolic >= 180 || $bp->diastolic >= 110) {
            $bp->category = "High blood pressure (Hypertensive crisis)";
        } elseif (($bp->systolic >= 160 && $bp->systolic < 180) || ($bp->diastolic >= 100 && $bp->diastolic < 110)) {
            $bp->category = "High blood pressure (Stage 2)";
        } elseif (($bp->systolic >= 140 && $bp->systolic < 160) || ($bp->diastolic >= 90 && $bp->diastolic < 100)) {
            $bp->category = "High blood pressure (Stage 1)";
        } elseif (($bp->systolic >= 130 && $bp->systolic < 140) || ($bp->diastolic >= 80 && $bp->diastolic < 90)) {
            $bp->category = "Pre-high blood pressure";
        } elseif ($bp->systolic < 90 || $bp->diastolic < 60) {
            $bp->category = "Low blood pressure";
        } elseif ($bp->systolic < 120 && $bp->diastolic < 80) {
            $bp->category = "Ideal blood pressure";
        } else {
            $bp->category = "Blood pressure classification not found.";
        }

        $bp->save();
        return response()->json(['success' => 'Blood pressure data stored successfully.'], 201);
    }
}
