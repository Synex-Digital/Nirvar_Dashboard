<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\BloodPressure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

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
                'avg_systolic'   => ceil($data->avg('systolic') ),
                'avg_diastolic'  => ceil($data->avg('diastolic') ),
            ], 200);
        }

    }
    public function blood_pressure_seven_days(){

        $user = auth('api')->user();
        // $data = BloodPressure::where('user_id', $user->id)
        //     ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
        //     ->orderBy('created_at', 'desc')
        //     ->get();
        $lastSevenDaysData = BloodPressure::where('user_id', $user->id)
        ->where('created_at', '>=', Carbon::now()->subDays(7))
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy(function ($date) {
            return $date->created_at->format('d-m-Y');
        });
        $averages = [];
        $avg_sys = 0;
        $avg_dia = 0;

        foreach ($lastSevenDaysData as $day => $data) {
            // For each day, calculate the average systolic and diastolic values
            $systolicAvg = ceil( $data->avg('systolic'));
            $diastolicAvg =ceil( $data->avg('diastolic'));
            $category = $this->category($systolicAvg, $diastolicAvg);
            $avg_sys += $systolicAvg;
            $avg_dia += $diastolicAvg;
            $averages[$day] = [
                'systolic_avg' => $systolicAvg,
                'diastolic_avg' => $diastolicAvg,
                'category' => $category
            ];
        }
        if(Empty($averages)){
            return response()->json([
                'status'    => 0,
                'message'   => "No data found",
            ], 200);
        }else{
            return response()->json([
                'status'    => 1,
                'message'   => "success",
                'data'      => $averages,
                'avg_systolic'   => ceil($avg_sys/count($averages)),
                'avg_diastolic'  => ceil($avg_dia/ count($averages)),
            ], 200);
        }

    }
    function getWeeklyAverage($userId, $startDate, $endDate) {
        return DB::table('blood_pressures')
            ->where('user_id', $userId)
            ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();
    }
    public function blood_pressure_weekly(){
        $user = auth('api')->user();
        // Get the current date and the first day of the current month
        // $currentDate = Carbon::now();
        // $firstDayOfMonth = $currentDate->copy();

        // Define the start date of each week in the current month
        // $weekOneStart = $firstDayOfMonth;
        // $weekTwoStart = $firstDayOfMonth->copy()->addDays(7);
        // $weekThreeStart = $firstDayOfMonth->copy()->addDays(14);
        // $weekFourStart = $firstDayOfMonth->copy()->addDays(21);
        // $weekFiveStart = $firstDayOfMonth->copy()->addDays(28);

// Initialize Carbon to manage date operations
$currentDate = Carbon::now();
$weekStart = $currentDate->startOfWeek();

$weeklyAverages = [];
for ($i = 0; $i < 4; $i++) {
    // Calculate start and end dates for each week
    $endDate = $weekStart->copy();
    $startDate = $weekStart->copy()->subWeek();

    // Get weekly average data
    $data = $this->getWeeklyAverage($user->id, $startDate, $endDate);

    // Store data in the array with proper formatting
    $weeklyAverages['Week ' . ($i + 1)] = [
        'avg_systolic' => $data->avg_systolic,
        'avg_diastolic' => $data->avg_diastolic,
        'category' => $this->category($data->avg_systolic, $data->avg_diastolic),
    ];

    // Move to the previous week
    $weekStart->subWeek();
}

return response()->json([
    'status' => 1,
    'message' => "success",
    'data' => $weeklyAverages,
], 200);
        // $dataOne =  DB::table('blood_pressures')
        //     ->where('user_id', $user->id)
        //     ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
        //     ->whereBetween('created_at', [$weekOneStart, $weekTwoStart])
        //     ->first();
        // $dataTwo =  DB::table('blood_pressures')
        //     ->where('user_id', $user->id)
        //     ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
        //     ->whereBetween('created_at', [$weekTwoStart, $weekThreeStart])
        //     ->first();
        // $dataThree = DB::table('blood_pressures')
        //     ->where('user_id', $user->id)
        //     ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
        //     ->whereBetween('created_at', [$weekThreeStart, $weekFourStart])
        //     ->first();
        // $dataFour = DB::table('blood_pressures')
        //     ->where('user_id', $user->id)
        //     ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
        //     ->whereBetween('created_at', [$weekFourStart, $weekFiveStart])
        //     ->first();
        // // Query to calculate weekly averages
        // $weeklyAverages = [
        //     'Week One' =>[
        //         'avg_systolic' => $dataOne->avg_systolic,
        //         'avg_diastolic' => $dataOne->avg_diastolic,
        //         'category' => $this->category($dataOne->avg_systolic, $dataOne->avg_diastolic),
        //     ],
        //     'Week Two' =>[
        //         'avg_systolic' => $dataTwo->avg_systolic,
        //         'avg_diastolic' => $dataTwo->avg_diastolic,
        //         'category' => $this->category($dataTwo->avg_systolic, $dataTwo->avg_diastolic),
        //     ] ,

        //     'Week Three' =>[
        //         'avg_systolic' => $dataThree->avg_systolic,
        //         'avg_diastolic' => $dataThree->avg_diastolic,
        //         'category' => $this->category($dataThree->avg_systolic, $dataThree->avg_diastolic),
        //     ] ,

        //     'Week Four' => [
        //         'avg_systolic' => $dataFour->avg_systolic,
        //         'avg_diastolic' => $dataFour->avg_diastolic,
        //         'category' => $this->category($dataFour->avg_systolic, $dataFour->avg_diastolic),
        //     ],
        // ];
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
       // Function to get start and end dates for a given month offset
        function getMonthRange($offset) {
            $start = Carbon::now()->subMonths($offset)->startOfMonth();
            $end = Carbon::now()->subMonths($offset)->endOfMonth();
            return [$start, $end];
        }

        // Function to calculate the monthly average
        function getMonthlyAverage($userId, $startDate, $endDate) {
            return DB::table('blood_pressures')
                ->where('user_id', $userId)
                ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->first();
        }
    //    $monthOneStart = $currentDate->copy()->startOfMonth();
    //    $monthOneEnd = $currentDate->copy()->endOfMonth();

    //    $monthTwoStart = $currentDate->copy()->subMonth(1)->startOfMonth();
    //    $monthTwoEnd = $currentDate->copy()->subMonth(1)->endOfMonth();

    //    $monthThreeStart =$currentDate->copy()->subMonth(3)->endOfMonth();
    //    $monthThreeEnd = $currentDate->copy()->subMonth(1)->startOfMonth();


    //    $monthFourStart = $currentDate->copy()->subMonth(3)->startOfMonth();
    //    $monthFourEnd = $currentDate->copy()->subMonth(3)->endOfMonth();
        // Loop to calculate the averages for the last four months
        $monthlyAverages = [];
        for ($i = 0; $i < 4; $i++) {
            list($startDate, $endDate) = getMonthRange($i);
            $data = getMonthlyAverage($user->id, $startDate, $endDate);

            if ($data) {
                $monthlyAverages['Month ' . ($i + 1)] = [
                    'avg_systolic' => $data->avg_systolic,
                    'avg_diastolic' => $data->avg_diastolic,
                    'category' => $this->category($data->avg_systolic, $data->avg_diastolic),
                ];
            } else {
                $monthlyAverages['Month ' . ($i + 1)] = [
                    'avg_systolic' => null,
                    'avg_diastolic' => null,
                    'category' => 'NA',  // No data for this month
                ];
            }
        }
        // // Query to calculate monthly averages and round them
        // $monthlyAverages = [
        //     'Month One' => DB::table('blood_pressures')
        //         ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
        //         ->whereBetween('created_at', [$monthOneStart, $monthOneEnd])
        //         ->first(),

        //     'Month Two' => DB::table('blood_pressures')
        //         ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
        //         ->whereBetween('created_at', [$monthTwoStart, $monthTwoEnd])
        //         ->first(),

        //     'Month Three' => DB::table('blood_pressures')
        //         ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
        //         ->whereBetween('created_at', [$monthThreeStart, $monthThreeEnd])
        //         ->first(),

        //     'Month Four' => DB::table('blood_pressures')
        //         ->select(DB::raw('ROUND(AVG(systolic)) as avg_systolic, ROUND(AVG(diastolic)) as avg_diastolic'))
        //         ->whereBetween('created_at', [$monthFourStart, $monthFourEnd])
        //         ->first(),
        // ];
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
            return response()->json([
                'status' => 0,
                'error' => 'You can only submit your blood pressure data twice within a 24-hour period.'
            ], 200);
        }

        $bp = new Bloodpressure();
        $bp->user_id = auth('api')->user()->id;
        $bp->systolic = $request->systolic;
        $bp->diastolic = $request->diastolic;
        $category = $this->category($request->systolic, $request->diastolic);
        $bp->category = $category;
        $bp->save();
        return response()->json([
            'status' => 1,
            'message' => 'Blood pressure data stored successfully.'
        ], 200);
    }


    function category($systolic , $diastolic){
        if($systolic == 0 || $diastolic == 0){
            return "NA";
        }
        $category = "";
        if ($systolic <= 90 || $diastolic <= 60) {
            $category = "Low";
        } elseif (($systolic > 90 && $systolic < 130) || ($diastolic > 60 && $diastolic < 80)) {
            $category = "Normal";
        }
        elseif (($systolic > 130 ) || ($diastolic > 80 )) {
            $category = "High";
        }
        else {
            $category = "NA";
        }
        return $category;

    }

}
