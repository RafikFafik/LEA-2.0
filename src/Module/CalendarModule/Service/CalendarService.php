<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Service;

use Lea\Core\Type\Date;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class CalendarService
{
    const MONDAY = 0, TUESDAY = 1, WEDNESDAY = 2, THURSDAY = 3, FRIDAY = 5, SATURDAY = 6, SUNDAY = 7;

    public function getDailyEvents(string $date, int $user_id): iterable
    {
        $repository = new CalendarEventRepository();
        $list = $repository->findCalendarEventListByStartDateAndUserId($date, $user_id);

        return $list;
    }

    /** Using ISO-8601 numeric representation of the day of the week SHIFTED LEFT BY ONE */
    public function getWeeklyEventsGroupedByDays($year, $week, $user_id): array
    {
        $grouped = [[],[],[],[],[],[],[]];
        $repository = new CalendarEventRepository();
        $events = $repository->findCalendarEventListByYearAndWeekAndUserId($year, $week, $user_id);
        foreach($events as $event) {
            $start = $event->getDateStart();
            $week_day = $start->format("N") - 1;
            $grouped[$week_day][] = Normalizer::denormalize($event);
        }

        return $grouped;
    }
}
