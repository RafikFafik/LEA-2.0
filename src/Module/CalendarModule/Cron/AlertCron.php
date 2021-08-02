<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Cron;

use Lea\Core\Logger\Logger;
use Lea\Core\Mailer\Mailer;
use Lea\Core\Type\DateTime;
use Lea\Core\Exception\EmailNotSentException;
use Lea\Core\PushNotification\PushNotification;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Core\Exception\PushNotificationNotSentException;
use Lea\Module\CalendarModule\Repository\CalendarAlertRepository;
use Lea\Module\ContractorModule\Repository\ContractorRepository;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class AlertCron
{
    public static function sendAlerts()
    {
        $cer = new CalendarEventRepository();
        $user_repository = new UserRepository();
        $contractor_repository = new ContractorRepository();
        $alert_repository = new CalendarAlertRepository();
        $current_minute_alerts = $alert_repository->findNotSentListByLaunchDateTime(new DateTime());
        // $current_minute_alerts = $alert_repository->findList();

        foreach ($current_minute_alerts as $alert) {
            if (!($alert->getKind() == 'email' || $alert->getKind() == 'push')) {
                Logger::save("Alert with no proper defined type: Expected `email` or `push`, got: " . $alert->getKind());
                continue;
            }

            $event = $cer->findById((int)$alert->getCalendarEventId());
            $user = $user_repository->findById($event->getUserId());
            $organizer = $user->getName() . ' ' . $user->getSurname();
            $contractor = $contractor_repository->findById($event->getContractorId());
            $time_info = $event->getDateStart() . ' ' . $event->getTimeStart() . ' - ' . $event->getTimeEnd();
            $subject = self::getSubject($event->getTitle());
            $users = $user_repository->findListByIds($event->getUserIds());
            $recipients = self::getRecipients($users, $alert->getKind());

            if ($alert->getKind() == 'email') {
                $body = self::getMailBody($event->getTitle(), $time_info, $organizer, $contractor->getShortname());
                self::handleEmail($recipients, $subject, $body);
            } elseif ($alert->getKind() == 'push') {
                $body = self::getPushNotificationBody($event->getTitle(), $time_info, $organizer, $contractor->getShortname());
                self::handlePushNotification($recipients, $subject, $body);
            }

            Logger::save("Alert about event " . $event->getId() . ": " .  $event->getTitle() . " was sent | Type : " . $alert->getKind());
        }
    }

    private static function getRecipients(array $users, string $strategy): array
    {
        $strategy =  $strategy == 'email' ? 'getEmail' : 'getMobileAppToken';
        foreach ($users as $user) {
            $recipients[] = $user->$strategy();
        }
        return $recipients;
    }

    private static function handlePushNotification($recipients, string $subject, $body): void
    {
        try {
            PushNotification::push($recipients, $subject, $body);
        } catch (PushNotificationNotSentException $e) {
            Logger::save("Push not sent: " . $e->getMessage());
        }
    }

    private static function handleEmail($recipients, string $subject, string $message): void
    {
        try {
            Mailer::sendMail($recipients, $subject, $message);
        } catch (EmailNotSentException $e) {
            Logger::save("Email not sent - Catched by AlertCron");
        }
    }

    private static function getSubject(string $title): string
    {
        return "Powiadomienie: $title";
    }

    private static function getBody(string $title, $time_info, string $organizer, string $contractor): string
    {
        return '<p><strong>' . $title . '</strong><p>' .
            "<p>Kiedy: $time_info</p>" .
            "<p>Kontrahent: $contractor</p>" .
            "<p>Organizator: $organizer</p>";
    }

    private static function getPushNotificationBody(string $title, $time_info, string $organizer, string $contractor): string
    {
        return "\n" .
            $title . "\n" .
            'Kiedy: ' . $time_info . "\n" .
            'Kontrahent: ' . $contractor . "\n" .
            'Organizator: ' . $organizer;
    }

    private static function getMailBody(string $title, $time_info, string $organizer, string $contractor): string
    {
        return
            '
        <body style="background-color: rgb(17, 17, 17);">
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Antic">
            <table style="color: aliceblue;background-color: rgb(31, 31, 31); border: 1px solid rgb(192, 173, 173); width: 30rem; font-family: Antic; padding: 1.5rem; margin-left: auto; margin-right: auto; margin-top: 3rem;">
                <tr">
                    <td style="font-size: 1.5rem; padding: 10px;">' . $title . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Kiedy: </td>
                    <td>' . $time_info . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Kontrahent: </td>
                    <td>' . $contractor . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Organizator: </td>
                    <td>' . $organizer . '</td>
                </tr>
            </table>
        </body>
        ';
    }
}
