<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Service\CalendarService;

class WeeklyScheduleController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                $service = new CalendarService();
                Response::ok($service->getWeeklyEventsGroupedByDays($this->params['year'], $this->params['week'], $this->params['user_id']));
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
