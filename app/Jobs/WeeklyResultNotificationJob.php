<?php

namespace App\Jobs;

use App\Models\User;
use Kreait\Firebase\Factory;
use App\Models\BloodPressure;
use App\Models\NotificationToken;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;

class WeeklyResultNotificationJob implements ShouldQueue
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
            $notifications[] = [
                'user_id' => $user->id,
                'most_frequent_category' => $mostFrequentCategory,
                'fcm_token' => NotificationToken::where('user_id', $user->id)->value('device_token'),
            ];
        } else {
            // No data for the week, you could add a notification or leave it out
            $notifications[] = [
                'user_id' => $user->id,
                'most_frequent_category' => 'No blood pressue data last week',
                'fcm_token' => NotificationToken::where('user_id', $user->id)->value('device_token'),
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

            // Prepare the message
            $message = CloudMessage::withTarget('token', $notification['fcm_token'])
                ->withNotification(Notification::create(
                    'Weekly Data Report',
                    "Last week's blood pressure readings was". $notification['most_frequent_category']."Please update your blood pressure and diabetes data."
                ))
                ->withData([
                    'unique_identifier' => 'daily_input_check',
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
