<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Repository;

use Lea\Core\Repository\Repository;
use Lea\Core\Type\DateTime;

final class CalendarAlertRepository extends Repository
{
    public function findNotSentListByLaunchDateTime(DateTime $launch_date_time): iterable
    {
        $constraints = ['launch_date_time' => $launch_date_time->__toString()];

        return $this->findList($constraints, [], false);
    }
}
