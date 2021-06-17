<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Repository;

use Lea\Core\Validator\Validator;
use Lea\Core\Repository\Repository;
use Lea\Module\CalendarModule\Entity\Event;
use Lea\Module\CalendarModule\Entity\CalendarEvent;

final class CalendarEventRepository extends Repository
{
    public function findCalendarEventListByStartDate(string $date, object $object): iterable
    {
        $res = self::getRecordsData($object, $date, 'date_start');

        return $res;
    }

    public function findCalendarEventListByMonthAndYear(string $month, string $year): iterable
    {
        $month = Validator::parseMonth($month);
        $constraint = $year . '-' . $month;
        $list = $this->getListDataByConstraints(new CalendarEvent, ['date_start_LIKE' => $constraint]);

        return $list;
    }
}
