<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\CalendarModule\Entity\Event;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Module\CalendarModule\Repository\CalendarRepository;

class DailyScheduleController implements ControllerInterface
{
    private $request;

    function __construct(Request $request, array $params = NULL)
    {
        $this->request = $request;
        $this->params = $params;
    }

    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $CalendarRepository = new CalendarRepository($this->params);
                    $list = $CalendarRepository->getEventListByStartDate($this->params['date'], new Event);
                    $res = Normalizer::denormalizeList($list);
                    Response::ok($res);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
            case "POST":
                Response::methodNotAllowed();

            case "DELETE":
                Response::methodNotAllowed();
            default:
                Response::methodNotAllowed();
        }
    }
}
