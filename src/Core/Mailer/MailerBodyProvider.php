<?php

namespace Lea\Core\Mailer;

class MailerBodyProvider
{
  public static function getAccountCreatedBodyMessage(string $token, string $system, string $name, string $surname): string
  {
    $user_app = $_ENV['USER_APP'];
    $body = '
          <div style="background-color: white; color: black;">
          <h1><big>Witaj ' . $name . ' ' . $surname .', za pomocą poniższego linku przejdź do formularza aktywacji konta w systemie '. $system . '</big></h1>
          <a href="' . $user_app . "/authorize/" . $token . '"><big>Link</big></a>
          </div>';

    return $body;
  }
}
