<?php

namespace Lea\Core\Mailer;

class MailerBodyProvider
{
    public static function getAccountCreatedBodyMessage(string $token, string $system, string $name, string $surname): string
    {
        $user_app = $_ENV['USER_APP'];
        $body = '
          <div style="background-color: white; color: black;">
          <h1><big>Witaj ' . $name . ' ' . $surname . ', za pomocą poniższego linku przejdź do formularza aktywacji konta w systemie ' . $system . '</big></h1>
          <a href="' . $user_app . "/authorize/" . $token . '"><big>Link</big></a>
          </div>';

        return $body;
    }

    public static function getResetPasswordBodyMessage(string $token, string $name, string $surname, string $system)
    {
        $user_app = $_ENV['USER_APP'];
        $statement = 'Witaj ' . $name . ' ' . $surname . ', za pomocą poniższego linku przejdź do formularza ustalania nowego hasła w systemie ' . $system;
        $url = '<a href="' . $user_app . "/resetPassword/" . $token . '"><big>Link</big></a>';


        return '
            <body style="background-color: rgb(17, 17, 17);">
                <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Antic">
                <table style="color: aliceblue;background-color: rgb(31, 31, 31); border: 1px solid rgb(192, 173, 173); width: 30rem; font-family: Antic; padding: 1.5rem; margin-left: auto; margin-right: auto; margin-top: 3rem;">
                    <tr">
                        <td style="font-size: 1.5rem; padding: 10px;">' . $statement . '</td>
                    </tr>
                    <tr">
                        <td style="font-size: 1.5rem; padding: 10px; text-decoration: none">' . $url . '</td>
                    </tr>
                </table>
            </body>
            ';
    }
}
