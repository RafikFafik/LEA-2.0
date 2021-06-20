<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Repository;

use Lea\Core\Validator\Validator;
use Lea\Core\Repository\Repository;
use Lea\Module\CalendarModule\Entity\Event;
use Lea\Module\CalendarModule\Entity\CalendarEvent;
use Lea\Module\Security\Service\AuthorizedUserService;

final class CalendarEventRepository extends Repository
{
    public function findCalendarEventListByStartDate(string $date, object $object): iterable
    {
        $res = self::getRecordsData($object, $date, 'date_start');

        return $res;
    }

    public function findCalendarEventListByConstraints($month, $year, int $user_id = null): iterable
    {
        $month = Validator::parseMonth($month);
        $constraint = $year . '-' . $month;
        $constraints = ['date_start_LIKE' => $constraint];
        $constraints['user_id'] = $user_id ? $user_id : AuthorizedUserService::getAuthorizedUserId();
        $list = $this->getListDataByConstraints(new CalendarEvent, $constraints);

        return $list;
    }
}
