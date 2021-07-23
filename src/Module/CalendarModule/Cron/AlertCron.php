<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Cron;

use Lea\Core\Exception\EmailNotSentException;
use Lea\Core\Logger\Logger;
use Lea\Core\Mailer\Mailer;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class AlertCron
{
    public static function sendAlerts()
    {
        $cer = new CalendarEventRepository();
        $user_repository = new UserRepository();

        $events = $cer->findTodayCalendarEventList();

        foreach ($events as $event) {
            foreach ($event->getAlerts() as $alert) {
                if ($alert->getKind() != 'email' || $alert->getLaunchDateTime() === null)
                    continue;

                if ($alert->getLaunchDateTime()->format('Y-m-d H:i') != date('Y-m-d H:i'))
                    continue;

                $user = $user_repository->findById($event->getUserId());
                $organizer = $user->getName() . ' ' . $user->getSurname();
                foreach ($event->getEmployees() as $employee) {
                    $user = $user_repository->findById($employee->getUserId());
                    $time_info = $event->getDateStart() . ' ' . $event->getTimeStart() . ' - ' . $event->getTimeEnd();
                    try {
                        Mailer::sendMail(
                            $user->getEmail(),
                            'Powiadomienie: ' . $event->getTitle() . ' - ' . $time_info,
                            '<p><strong>' . $event->getTitle() . '</strong><p>' .
                            "<p>Kiedy: $time_info</p>" .
                            "<p>Organizator: $organizer</p>"
                        );
                    } catch (EmailNotSentException $e) {
                    }
                }
                Logger::save("Alert about event " . $event->getId() . ": " .  $event->getTitle() . " was sent");
            }
        }
    }
}
