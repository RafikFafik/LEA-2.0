<?php

declare(strict_types=1);

namespace Lea\Core\PushNotification;

use Lea\Core\Exception\PushNotificationNotSentException;
use Lea\Response\Response;

final class PushNotification
{
    public static function push($recipientOrRecipients, string $subject, string $message): void
    {
        self::verifyRecipients($recipientOrRecipients);
        if (is_array($recipientOrRecipients))
            $body['registration_ids'] = $recipientOrRecipients;
        else
            $body['to'] = $recipientOrRecipients;

        $body["notification"] = [
            "title" => $subject,
            "body" => $message,
            "mutable_content" => true,
            "sound" => "Tri-tone"
        ];
        $body["data"] = [
            "url" => "",
            "dl" => ""
        ];
        $headers = [
            "Content-Type: application/json; charset=UTF-8",
            "Authorization: " . $_ENV['FIREBASE_HEADER']
        ];
        $handle = curl_init($_ENV['FIREBASE_URL']);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($body));



        $response_body = curl_exec($handle);
        $response_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        if ($response_code != 200) {
            throw new PushNotificationNotSentException($response_body);
        }
        Response::ok($response_body);
    }

    private static function verifyRecipients($recipientOrRecipients): void
    {
        /* TODO */
        //     if(!is_array($recipientOrRecipients))
        //         if($recipientOrRecipients === null || empty($recipientOrRecipients))
        //             throw new InvalidMobileAppTokenException;
        //         foreach($recipientOrRecipients as $recipient) {
        //             if()
        //         }
        //     }
    }
}
