<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Diabetes;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

use App\Models\BloodPressure;
use App\Models\NotificationToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;

class NotificationContoller extends Controller
{
    public function index(){
        $today = Carbon::now();
        $dayName = $today->format('l');
        return view('dashboard.admin.notification.index',[
            'dayName' => $dayName,
        ]);
    }
    public function adminNotification_weeklyBloodPressure(){
        $today = Carbon::now();
        $dayName = $today->format('l');
        if($dayName !== "Saturday"){
            flash()->options(['position' => 'bottom-right'])->error('Weekly notification can only be sent on Saturday');
            return back();
        }
        $users = User::where('role', 'patient')
        ->whereNotNull('register_at')
        ->get();

        $bloodPressue = []; // To store all notifications to send
        $diabetes = []; // To store all notifications to send

        // Fetch device tokens in bulk before the loop
        $deviceTokens = NotificationToken::whereIn('user_id', $users->pluck('id'))
            ->get()
            ->keyBy('user_id')
            ->map(fn($token) => $token->device_token);

        foreach ($users as $user) {
            // Get the categories for blood pressure readings within the current week
            $categories = BloodPressure::where('user_id', $user->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->pluck('category'); // Pluck only the 'category' field

            // Count the occurrences of each category
            $categoriesCount = $categories->countBy(); // count occurrences of each category

            // If we have data for the week
            if ($categoriesCount->isNotEmpty()) {
                // Find the most frequent category
                $mostFrequentCategory = $categoriesCount->sortDesc()->keys()->first();

                // Add to notifications array
                $bloodPressue[] = [
                    'user_id' => $user->id,
                    'most_frequent_category' => $mostFrequentCategory,
                    'fcm_token' => $deviceTokens[$user->id] ?? null, // Safely access token
                ];
            } else {
                // No data for the week, you could add a notification or leave it out
                $bloodPressue[] = [
                    'user_id' => $user->id,
                    'most_frequent_category' => 'No blood pressure data last week',
                    'fcm_token' => $deviceTokens[$user->id] ?? null, // Safely access token
                ];
            }
        }
        $this->sendBatchNotifications($bloodPressue);
        return back();
    }

    public function sendBatchNotifications(array $notifications): void
    {

        $firebaseCredentials = config('services.firebase.credentials');
        $messaging = (new Factory)
            ->withServiceAccount($firebaseCredentials)
            ->createMessaging();

        foreach ($notifications as $notification) {
            if (empty($notification['fcm_token'])) {
                Log::warning("FCM token missing for user: {$notification['user_id']}");
                continue;
            }
            $new = Notification::create(
                'Weekly Data Report',
                "Last week's blood pressure reading was hight"
            );
            // Prepare the message
            $message = CloudMessage::withTarget('token', $notification['fcm_token'])
                ->withNotification($new)
                ->withData([
                    'unique_identifier' => 'weekly_report',
                ]);
            // Try sending the notification
            try {
                $messaging->send($message);
                Log::info("Notification sent to user {$notification['user_id']}");
            } catch (MessagingException $e) {
                Log::error("FCM Messaging Error for user {$notification['user_id']}: " . $e->getMessage());
            }
        }
    }

}
