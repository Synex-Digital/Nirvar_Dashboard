<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Diabetes;
use Kreait\Firebase\Factory;
use App\Models\BloodPressure;
use App\Models\NotificationToken;


use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;


class DailyInputCheckJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
                $users = User::where('role', 'patient')->where('register_at', '!=', null)->get();
        $notifications = []; // To store all notifications to send

        foreach ($users as $user) {
            // Check if the user has recent blood pressure data
            $hasRecentBloodPressure = BloodPressure::where('user_id', $user->id)
                ->whereDate('created_at', '>=', now()->today())
                ->exists();  // directly check if the record exists within today
            // print_r($hasRecentBloodPressure);
            // Set $x to true or false based on the existence of recent blood pressure data
            $x = $hasRecentBloodPressure ? true : false;

            // Debug output for $hasRecentBloodPressure
            // print_r($x);  // This will print '1' (true) or '' (false)

            // Check if the user has recent diabetes data
            $hasRecentDiabetesData = Diabetes::where('user_id', $user->id)
                ->whereDate('created_at', '>=', now()->subDays(2))
                ->exists(); // check if the record exists in the last 2 days

            // Set $y to true or false based on the existence of recent diabetes data
            $y = $hasRecentDiabetesData ? true : false;

            // If either blood pressure or diabetes data is missing, create a notification
            if (!$x || !$y) {
                $notifications[] = [
                    'user_id' => $user->id,
                    'fcm_token' => NotificationToken::where('user_id', $user->id)->value('device_token'),
                    'missing_data' => !$x ? 'Blood Pressure' : 'Diabetes', // indicate which data is missing
                ];
            }
        }

        // Send notifications
        $this->sendBatchNotifications($notifications);
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

            $message = CloudMessage::withTarget('token', $notification['fcm_token'])
                ->withNotification(Notification::create(
                    'Reminder',
                    "Please update your blood pressure and diabetes data."
                ))
                ->withData([
                    'unique_identifier' => 'daily_input_check',
                ]);

            try {
                $messaging->send($message);
                Log::info("Notification sent to user {$notification['user_id']}");
            } catch (MessagingException $e) {
                Log::error("FCM Messaging Error for user {$notification['user_id']}: " . $e->getMessage());
            }
        }
    }

}
