<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Service;

use IntlDateFormatter;
use Lea\Core\Type\DateTime;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Type\DateTimeImmutable;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class CalendarService
{
    const MONDAY = 0, TUESDAY = 1, WEDNESDAY = 2, THURSDAY = 3, FRIDAY = 4, SATURDAY = 5, SUNDAY = 6;
    const DAY_L10N = [
        "Poniedziałek",
        "Wtorek",
        "Środa",
        "Czwartek",
        "Piątek",
        "Sobota",
        "Niedziela"
    ];
    const MONTH_L10N = ["Stycznia", "Lutego", "Marca", "Kwietnia", "Maja", "Czerwca", "Lipca", "Sierpnia", "Września", "Października", "Listopada", "Grudnia"];
    const SHIFT_MONTH_INDEX = 1;
    const FIRST_DAY_OF_WEEK = self::MONDAY;
    const LAST_DAY_OF_WEEK = self::SUNDAY;

    public function getDailyEvents(string $date, int $user_id): iterable
    {
        $repository = new CalendarEventRepository();
        $list = $repository->findCalendarEventListByStartDateAndUserId($date, $user_id);

        return $list;
    }

    /** Using ISO-8601 numeric representation of the day of the week SHIFTED LEFT BY ONE */
    public function getWeeklyEventsGroupedByDays($year, $week, $user_id): array
    {
        $weekdays = $this->getWeekInfo($year, $week);
        $repository = new CalendarEventRepository();
        $events = $repository->findCalendarEventListByYearAndWeekAndUserId($year, $week, $user_id);
        foreach ($events as $event) {
            $start = $event->getDateStart();
            $week_day = $start->format("N") - 1;
            $weekdays[$week_day]["events"][] = Normalizer::denormalize($event);
        }

        return $weekdays;
    }

    public function getWeekInfo($year, $week): array
    {
        $date = new DateTimeImmutable('today');
        // $polishDateFormatter = new IntlDateFormatter(
        //     'pl_PL',
        //     IntlDateFormatter::LONG,
        //     IntlDateFormatter::NONE
        // );
        for ($i = self::FIRST_DAY_OF_WEEK; $i <= self::LAST_DAY_OF_WEEK; $i++) {
            $day = $date->setISODate($year, $week, $i + 1);
            $weekdays[$i]["date_info"] = [
                "day_name" => self::DAY_L10N[$i],
                "day_number" => $day->format("d"),
                "month" => self::MONTH_L10N[$day->format('n') - self::SHIFT_MONTH_INDEX],
                "year" => $day->format('Y')
            ];
            $weekdays[$i]["events"] = [];
        }
        return $weekdays;
    }
}
