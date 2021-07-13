<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Service;

use Lea\Core\Type\Date;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class CalendarService
{
    public function getDailyEvents(string $date, int $user_id): iterable
    {
        $repository = new CalendarEventRepository();
        $list = $repository->findCalendarEventListByStartDate($date, $user_id);
        
        return $list;
    }
}
