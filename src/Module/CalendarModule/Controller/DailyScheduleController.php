<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Dotenv\Validator;
use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\CalendarModule\Entity\Event;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Repository\CalendarRepository;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class DailyScheduleController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                $repo = new CalendarEventRepository($this->params);
                $list = $repo->findCalendarEventListByStartDate($this->params['date'], new Event);
                $res = Normalizer::denormalizeList($list);
                Response::ok($res);
            default:
                Response::methodNotAllowed();
        }
    }
}
