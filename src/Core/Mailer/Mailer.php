<?php

namespace Lea\Core\Mailer;

use Throwable;
use Lea\Core\Logger\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use Lea\Core\Exception\EmailNotSentException;
use Lea\Core\Exception\InvalidEmailAddressException;

class Mailer
{
    /**
     * @param $recipientOrRecipients - string that contains a valid email address or array of email addresses
     */
    public static function sendMail($recipientOrRecipients, string $subject, string $body): void
    {
        $mail = self::getConfiguredMailer();
        if(is_string($recipientOrRecipients)) {
            self::validateEmail($recipientOrRecipients);
            $mail->addAddress($recipientOrRecipients);
        } elseif(is_array($recipientOrRecipients)) {
            foreach ($recipientOrRecipients as $recipient) {
                self::validateEmail($recipient);
                $mail->addAddress($recipient);
            }
        }
        $mail->Subject = $subject;
        $mail->Body = $body;
        self::execute($mail);
    }

    private static function getConfiguredMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = $_ENV['MAIL_SMTP'];
        $mail->Port = $_ENV['MAIL_PORT'];
        $mail->clearAddresses();
        $mail->setFrom(strtolower($_ENV['TENANT']) . '@lea24.pl', $_ENV['TENANT']);
        $mail->isHTML(true);

        return $mail;
    }

    private static function execute(PHPMailer $mail): void
    {
        try {
            $mail->send();
        } catch (Throwable $t) {
            Logger::save("Email not sent exception throwed");
            throw new EmailNotSentException;
        }
    }

    private static function validateEmail(string $email): void
    {
        if (!PHPMailer::validateAddress($email)) {
            Logger::save("Invalid email address exception throwed, got $email");
            throw new InvalidEmailAddressException("Got: $email");
        }
    }
}
