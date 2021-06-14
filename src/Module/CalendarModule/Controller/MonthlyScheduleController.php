<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\CalendarModule\Entity\Event;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class MonthlyScheduleController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                $event_repository = new CalendarEventRepository($this->params);
                $list = $event_repository->findCalendarEventListByMonthAndYear($this->params['month'], $this->params['year']);
                $res = Normalizer::denormalizeList($list);
                Response::ok($res);
            default:
                Response::methodNotAllowed();
        }
    }
}
