<?php

namespace App\Http\Controllers\Api;

use App\Models\Diabetes;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DiabetesController extends Controller
{
   public function diabetes_store(Request $request){

        $validate = Validator::make($request->all(), [
            'blood_sugar_level' => 'required|numeric|min:0',
        ]);
        // if validation fails
        if ($validate->fails()) {
            return response()->json([
                'status'    => 0,
                'message'   => $validate->errors()->messages(),
            ],200);
        }

        // Get the currently authenticated user
        $user = auth('api')->user();

        // Check the number of submissions in the last 24 hours
        $submissionCount = Diabetes::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->count();

        // if ($submissionCount >= 2) {
        //     return response()->json([
        //         'status' => 0,
        //         'message' => 'You can only make 2 submissions in the last 24 hours.'
        //     ], 200);
        // }
        $diabetes = new Diabetes();
        $diabetes->user_id = $user->id;
        $diabetes->blood_sugar_level = $request->blood_sugar_level;
        $diabetes->category = $this->category($request->blood_sugar_level);
        $diabetes->save();
        return response()->json([
            'status' => 1,
            'message' => 'Blood sugar data stored successfully.'
        ], 200);

   }

   public function diabetes_today(){
        $user = auth('api')->user();
        $data = Diabetes::where('user_id', $user->id)
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
                'avg_level' => number_format($data->avg('blood_sugar_level'), 1),
                // 'data'      => $data,
                // 'minimum'   => number_format($data->min('blood_sugar_level'), 1),
                // 'maximum'   => number_format($data->max('blood_sugar_level'), 1),

            ], 200);
        }

    }
   public function diabetes_seven_days(){
        $user = auth('api')->user();
        $data = Diabetes::where('user_id', $user->id)
        ->where('created_at', '>=', Carbon::now()->subDays(7))
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy(function ($date) {
            return $date->created_at->format('d-m-Y');
        });
        $averages = [];
        foreach ($data as $day => $values) {
            if ($values->count() == 2) {
                // Assume that the collection has exactly two values, get them directly
                $value_one = $values->first()->blood_sugar_level;
                $value_two = $values->last()->blood_sugar_level;
            } else {
                // Handle cases where there might not be exactly two values
                $value_one = $values->first()->blood_sugar_level;
                $value_two = $values->count() > 1 ? $values->last()->blood_sugar_level : $value_one; // Duplicate or use only one value
            }
            $average = number_format(($value_one + $value_two) / 2, 1);
            $category = $this->category($average); // Assuming category() determines category based on the average
            $averages[$day] = [
                'value_one' => number_format($value_one, 1),
                'value_two' => number_format($value_two, 1),
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
            ], 200);
        }
    }
    function getWeeklyAverage($userId, $startDate, $endDate) {
        return DB::table('diabetes')
            ->where('user_id', $userId)
            ->select(DB::raw('ROUND(AVG(blood_sugar_level), 1) as avg_level'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();
    }
    public function diabetes_weekly(){
        $user = auth('api')->user();
    // Get the current date and start of the current week
    $currentDate = Carbon::now();
    $weekStart = $currentDate->startOfWeek();

    $weeklyAverages = [];
    for ($i = 0; $i < 4; $i++) {
        // Calculate end date and start date for each week
        $endDate = $weekStart->copy();
        $startDate = $weekStart->copy()->subWeek();

        // Get weekly average data
        $data = $this->getWeeklyAverage($user->id, $startDate, $endDate);

        $average = $data->avg_level;

        // Store data in the array with proper formatting
        $weeklyAverages['Week ' . ($i + 1)] = [
            'avg_level' => $average,
            'category' => $this->category($average),
        ];

        // Move to the previous week
        $weekStart->subWeek();
    }

        //Query to calculate weekly averages
        // $weeklyAverages = [
        //     'Week One' => DB::table('diabetes')
        //         ->where('user_id', $user->id)
        //         ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
        //         ->whereBetween('created_at', [ $weekOneStart,$weekTwoStart])
        //         ->first(),

        //     'Week Two' => DB::table('diabetes')
        //         ->where('user_id', $user->id)
        //         ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
        //         ->whereBetween('created_at', [$weekTwoStart, $weekThreeStart])
        //         ->first(),

        //     'Week Three' => DB::table('diabetes')
        //         ->where('user_id', $user->id)
        //         ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
        //         ->whereBetween('created_at', [$weekThreeStart, $weekFourStart])
        //         ->first(),

        //     'Week Four' => DB::table('diabetes')
        //         ->where('user_id', $user->id)
        //         ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
        //         ->whereBetween('created_at', [$weekFourStart, $weekFiveStart])
        //         ->first(),
        // ];
        // Fetch data for the past 4 weeks
        return response()->json([
            'status'    => 1,
            'message'   => "success",
            'data'      => $weeklyAverages,
        ], 200);

    }
    public function diabetes_monthly(){
        $user = auth('api')->user();
       // Get the current date and define the start of the last four months
       $currentDate = Carbon::now();
       function getMonthRange($offset) {
        $start = Carbon::now()->subMonths($offset)->startOfMonth();
        $end = Carbon::now()->subMonths($offset)->endOfMonth();
        return [$start, $end];
    }
    function getMonthlyAverage($userId, $startDate, $endDate) {
        return DB::table('diabetes')
            ->where('user_id', $userId)
            ->select(DB::raw('ROUND(AVG(blood_sugar_level),1) as
            avg_level'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();
    }
    $monthlyAverages = [];
    for ($i = 0; $i < 4; $i++) {
        list($startDate, $endDate) = getMonthRange($i);
        $data = getMonthlyAverage($user->id, $startDate, $endDate);

        if ($data) {
            $average = number_format( $data->avg_level,1);
            $monthlyAverages['Month ' . ($i + 1)] = [
               'avg_level' => $average,
                'category' => $this->category($average),
            ];
        } else {
            $monthlyAverages['Month ' . ($i + 1)] = [
                'avg_systolic' => null,
                'avg_diastolic' => null,
                'category' => 'NA',  // No data for this month
            ];
        }
    }
    //    $monthOneStart = $currentDate->copy()->startOfMonth();
    //    $monthOneEnd = $currentDate->copy()->endOfMonth();

    //    $monthTwoStart = $currentDate->copy()->subMonth(1)->startOfMonth();
    //    $monthTwoEnd = $currentDate->copy()->subMonth(1)->endOfMonth();
    //    $monthThreeStart =$currentDate->copy()->subMonth(3)->endOfMonth();
    //    $monthThreeEnd = $currentDate->copy()->subMonth(1)->startOfMonth();


    //    $monthFourStart = $currentDate->copy()->subMonth(3)->startOfMonth();
    //    $monthFourEnd = $currentDate->copy()->subMonth(3)->endOfMonth();

        // Query to calculate monthly averages and round them
        // $monthlyAverages = [
        //     'Month One' => DB::table('diabetes')
        //          ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
        //         ->whereBetween('created_at', [$monthOneStart, $monthOneEnd])
        //         ->first(),

        //     'Month Two' => DB::table('diabetes')
        //         ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
        //         ->whereBetween('created_at', [$monthTwoStart, $monthTwoEnd])
        //         ->first(),

        //     'Month Three' => DB::table('diabetes')
        //         ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
        //         ->whereBetween('created_at', [$monthThreeStart, $monthThreeEnd])
        //         ->first(),

        //     'Month Four' => DB::table('diabetes')
        //         ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
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
    public function category($value){
        if($value == 0){
            return  "NA";
        }
        $category = "";
        if ($value < 3.9) {
            $category = "Low";
        } elseif ($value>= 4 && $value <= 7) {
            $category = "Normal";
        } elseif ($value > 7) {
            $category = "High";
        }
        else {$category = "NA";
        }

        return $category;
    }
}
