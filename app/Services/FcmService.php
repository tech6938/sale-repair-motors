<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;

class FcmService
{
    /**
     * FcmService constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Send a notification to the user with the given token.
     *
     * @param string $token
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendNotification(string $token, string $title, string $body, array $data = [])
    {
        try {
            $message = CloudMessage::new()
                ->toToken($token)
                ->withNotification(
                    Notification::create($title, $body)
                )
                ->withData($data);

            $messaging = app(Messaging::class);

            $messaging->send($message);
        } catch (MessagingException $e) {
            logger()->error('FCM Error: ' . $e->getMessage());
        }
    }
}
