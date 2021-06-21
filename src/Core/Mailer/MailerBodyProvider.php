<?php

namespace Lea\Core\Mailer;

class MailerBodyProvider
{
  public static function getAccountCreatedBodyMessage(string $token, string $system): string
  {
    $user_app = $_ENV['USER_APP'];
    $body = '
          <div style="background-color: white; color: black;">
          <h1><big>Za pomocą poniższego linku przejdź do formularza aktywacji konta w systemie '. $system . '</big></h1>
          <a href="' . $user_app . '/security/confirm?token=' . $token . '"><big>Link</big></a>
          </div>';

    return $body;
  }
}
