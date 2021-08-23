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

class DailyScheduleController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                $service = new CalendarService();
                $list = $service->getDailyEvents($this->params['date'], $this->params['user_id'] ?? AuthorizedUserService::getAuthorizedUserId());
                $res = Normalizer::denormalizeList($list);
                $res = Normalizer::jsonToArrayList($res, 'employees');
                Response::ok($res);
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
