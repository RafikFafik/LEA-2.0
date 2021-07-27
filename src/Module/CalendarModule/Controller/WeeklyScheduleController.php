<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Security\Service\AuthorizedUserService;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;
use Lea\Module\CalendarModule\Service\CalendarService;

class WeeklyScheduleController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                $repository = new CalendarEventRepository();
                $list = $repository->findCalendarEventListByYearAndWeekAndUserId($this->params['year'], $this->params['week'], $this->params['user_id']);
                Response::ok(Normalizer::denormalizeList($list));
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
