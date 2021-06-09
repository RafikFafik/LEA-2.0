<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\CalendarModule\Entity\Event;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Repository\CalendarRepository;

class DailyScheduleController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                $CalendarRepository = new CalendarRepository($this->params);
                $list = $CalendarRepository->getEventListByStartDate($this->params['date'], new Event);
                $res = Normalizer::denormalizeList($list);
                Response::ok($res);
            default:
                Response::methodNotAllowed();
        }
    }
}
