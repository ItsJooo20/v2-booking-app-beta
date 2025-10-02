<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Exception;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase-credentials.json'));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Kirim notifikasi ke single device token dengan gambar
     */
    public function sendToToken(string $token, string $title, string $body, array $data = [], ?string $imageUrl = null)
    {
        try {
            $messageArray = [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ];

            // kalau ada gambar, tambahin ke payload
            if ($imageUrl) {
                $messageArray['notification']['image'] = $imageUrl;
            }

            $message = CloudMessage::fromArray($messageArray);

            $this->messaging->send($message);

            return [
                'success' => true,
                'message' => 'Notification sent successfully'
            ];
        } catch (Exception $e) {
            Log::error('Firebase notification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Kirim notifikasi ke multiple device tokens dengan gambar
     */
    public function sendToMultipleTokens(array $tokens, string $title, string $body, array $data = [], ?string $imageUrl = null)
    {
        try {
            $messageArray = [
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ];

            if ($imageUrl) {
                $messageArray['notification']['image'] = $imageUrl; // << ini yang penting
            }

            $message = CloudMessage::fromArray($messageArray);

            $report = $this->messaging->sendMulticast($message, $tokens);

            return [
                'success' => true,
                'successful' => $report->successes()->count(),
                'failed' => $report->failures()->count(),
                'message' => 'Notifications sent'
            ];
        } catch (Exception $e) {
            Log::error('Firebase multicast notification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}