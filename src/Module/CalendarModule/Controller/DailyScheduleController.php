<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Entity\CalendarEvent;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class DailyScheduleController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                $repo = new CalendarEventRepository;
                $list = $repo->findCalendarEventListByStartDate($this->params['date'], new CalendarEvent);
                $res = Normalizer::denormalizeList($list);
                $res = Normalizer::jsonToArrayList($res, 'employees');
                Response::ok($res);
            default:
                Response::methodNotAllowed();
        }
    }
}
