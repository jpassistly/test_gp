<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;

class NotificationController extends Controller
{
    protected $messaging;

    public function __construct()
    {
        // Initialize Firebase with the service account
        $factory = (new Factory)->withServiceAccount(base_path('app/Http/gramathupaal-b6b9d-firebase-adminsdk-2dggw-85a2f77efe.json'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification($user_fcm, $title, $message_body, $image = null, $link = null)
    {
        // Create the notification with an image if provided
        $notification = Notification::create($title, $message_body, $image);

        // Build the message with the target token
        $message = CloudMessage::withTarget('token', $user_fcm)
            ->withNotification($notification);

        // Include the link in the data payload if provided
        if ($link) {
            $message = $message->withData([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'link' => $link
            ]);
        }

        try {
            // Send the notification
            $this->messaging->send($message);
            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully'
            ]);
        } catch (MessagingException $e) {
            // Handle errors
            return response()->json([
                'success' => false,
                'notification_success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage(),
            ]);
        }
    }

    public function test()
    {
        // Example FCM token for a user
        $user_fcm = 'eZXcvWlrTDKl0eqQfJuF5B:APA91bFroufT8vngLNFTcxhLWfWGt_vfxrw6syOhzD6UBFwHH_yf6JW-OXterC-DpRtwGt1Fo42lisQXlKapQoqKHjwP4bYlsegKOaIN0RS97LLRgwEPvAaTAPE2NXG4xP_9AbiJQNO9'; // Replace with actual FCM token

        // Notification details
        $title = 'Test Notification Title';
        $message_body = 'This is the body of the test notification.';
        $image = 'https://www.example.com/image.jpg';
        $link = 'https://www.example.com';

        // Call the sendNotification method
        return $this->sendNotification($user_fcm, $title, $message_body, $image, $link);
    }
}
