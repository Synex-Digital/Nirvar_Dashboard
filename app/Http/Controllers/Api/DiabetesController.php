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

        if ($submissionCount >= 2) {
            return response()->json([
                'status' => 0,
                'message' => 'You can only make 2 submissions in the last 24 hours.'
            ], 200);
        }
        $diabetes = new Diabetes();
        $diabetes->user_id = $user->id;
        $diabetes->blood_sugar_level = $request->blood_sugar_level;

        if ($diabetes->blood_sugar_level < 3.9) {
            $diabetes->category = "Low Sugar(Hypoglycemia)";
        } elseif ($diabetes->blood_sugar_level >= 4 && $diabetes->blood_sugar_level <= 7) {
            $diabetes->category = "Normal";
        } elseif ($diabetes->blood_sugar_level > 7) {
            $diabetes->category = "High";
        }
        else {
            $diabetes->category = "Blood sugar classification not found.";
        }

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
                // 'data'      => $data,
                'minimum'   => number_format($data->min('blood_sugar_level'), 1),
                'maximum'   => number_format($data->max('blood_sugar_level'), 1),

            ], 200);
        }

    }
   public function diabetes_seven_days(){
        $user = auth('api')->user();
        $data = Diabetes::where('user_id', $user->id)
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
    public function diabetes_weekly(){
        $user = auth('api')->user();
        // Get the current date and the first day of the current month
        $currentDate = Carbon::now();
        $firstDayOfMonth = $currentDate->copy()->startOfMonth();

        // Define the start date of each week in the current month
        $weekOneStart = $firstDayOfMonth;
        $weekTwoStart = $firstDayOfMonth->copy()->subDays(7);
        $weekThreeStart = $firstDayOfMonth->copy()->subDays(14);
        $weekFourStart = $firstDayOfMonth->copy()->subDays(21);
        $weekFiveStart = $firstDayOfMonth->copy()->subDays(28);

        //Query to calculate weekly averages
        $weeklyAverages = [
            'Week One' => DB::table('diabetes')
                ->where('user_id', $user->id)
                ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
                ->whereBetween('created_at', [ $weekTwoStart,$weekOneStart])
                ->first(),

            'Week Two' => DB::table('diabetes')
                ->where('user_id', $user->id)
                ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
                ->whereBetween('created_at', [$weekTwoStart, $weekThreeStart])
                ->first(),

            'Week Three' => DB::table('diabetes')
                ->where('user_id', $user->id)
                ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
                ->whereBetween('created_at', [$weekThreeStart, $weekFourStart])
                ->first(),

            'Week Four' => DB::table('diabetes')
                ->where('user_id', $user->id)
                ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
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
    public function diabetes_monthly(){
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
            'Month One' => DB::table('diabetes')
                 ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
                ->whereBetween('created_at', [$monthOneStart, $monthOneEnd])
                ->first(),

            'Month Two' => DB::table('diabetes')
                ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
                ->whereBetween('created_at', [$monthTwoStart, $monthTwoEnd])
                ->first(),

            'Month Three' => DB::table('diabetes')
                ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
                ->whereBetween('created_at', [$monthThreeStart, $monthThreeEnd])
                ->first(),

            'Month Four' => DB::table('diabetes')
                ->select(DB::raw('MIN(blood_sugar_level) as minimum, MAX(blood_sugar_level) as maximum'))
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
}
