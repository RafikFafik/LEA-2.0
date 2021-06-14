<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Repository;

use Lea\Core\Repository\Repository;
use Lea\Core\Validator\Validator;
use Lea\Module\CalendarModule\Entity\Event;

final class CalendarEventRepository extends Repository
{
    private $entity;

    public function __construct(array $params)
    {
        $this->entity = new Event();
        parent::__construct($this->entity);
    }

    public function findCalendarEventListByStartDate(string $date, object $object): iterable
    {
        $res = self::getRecordsData($object, $date, 'date_start');

        return $res;
    }

    public function findCalendarEventListByMonthAndYear(string $month, string $year): iterable
    {
        $month = Validator::parseMonth($month);
        $constraint = $year . '-' . $month;
        $list = $this->getListDataByConstraints(new Event, ['date_start_LIKE' => $constraint]);

        return $list;
    }
}
