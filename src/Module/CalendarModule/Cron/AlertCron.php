<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Cron;

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

                foreach ($event->getEmployees() as $employee) {
                    $user = $user_repository->findById($employee->getUserId());
                    Mailer::sendMail(
                        $user->getEmail(),
                        'Nadchodzące spotkanie',
                        'Spotkanie ' . $event->getTitle() . ' rozpocznie się za ' . $alert->getTime() . ' minut'
                    );
                }
                Logger::save("Alert about event " . $event->getId() . ": " .  $event->getTitle() . " was sent");
            }
        }
    }
}
