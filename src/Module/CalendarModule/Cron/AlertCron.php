<?php



namespace Lea\Module\CalendarModule\Cron;

use Lea\Core\Mailer\Mailer;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Module\CalendarModule\Entity\CalendarEvent;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class AlertCron
{

	public static function sendAlerts()
	{
		$cer = new CalendarEventRepository();
		$user_repository = new UserRepository();

		$events = $cer->findCalendarEventListByStartDate(date('Y-m-d'), new CalendarEvent);

		foreach ($events as $event) {
			foreach ($event->getAlerts() as $alert) {
				if ($alert->getType() != 'email')
					continue;

				if ($event->getTimeStart() != date('H:i', strtotime('+' . $alert->getTime() . ' minutes')))
					continue;


				foreach (array_merge($event->getEmployees(), [$event->getUserId()]) as $employee) {
					$user = $user_repository->findById($employee);
					Mailer::sendMail(
						$user->getEmail(),
						'Nadchodzące spotkanie',
						'Spotkanie ' . $event->getTitle() . ' rozpocznie się za ' . $alert->getTime() . ' minut'
					);
				}
			}
		}
	}
}
