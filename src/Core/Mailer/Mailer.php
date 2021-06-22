<?php

namespace Lea\Core\Mailer;

use Throwable;
use PHPMailer\PHPMailer\PHPMailer;
use Lea\Core\Exception\EmailNotSentException;

class Mailer
{
  public static function sendMail(string $recipitient, string $subject, string $body): void
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

    // Recipients
    $mail->setFrom('sempre@lea24.pl', 'Sempre');
    $mail->clearAddresses();
    $mail->addAddress($recipitient);

    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body = $body;
    try {
      $mail->send();
    } catch (Throwable $t) {
      throw new EmailNotSentException;
    }
  }
}
