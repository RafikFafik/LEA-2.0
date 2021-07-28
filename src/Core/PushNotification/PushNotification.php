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
        // $handle = curl_init($_ENV['FIREBASE_URL']);
        $handle = curl_init("http://localhost:2020");
        curl_setopt($handle, CURLOPT_HEADER, $headers);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        // this function is called by curl for each header received
        $headers2 = [];
        curl_setopt(
            $handle,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use (&$headers2) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;

                $headers2[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
            }
        );

        $response_body = curl_exec($handle);
        $response_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        if ($response_code != 200) {
            Response::internalServerError($response_body);
            throw new PushNotificationNotSentException($response_body);
        }
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
