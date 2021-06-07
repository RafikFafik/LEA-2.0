<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\CalendarModule\Entity\Event;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Repository\CalendarRepository;

class EventCollectionController implements ControllerInterface
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
                $CalendarRepository = new CalendarRepository($this->params);
                $list = $CalendarRepository->getList(new Event);
                $res = Normalizer::denormalizeList($list);

                Response::ok($res);
            case "POST":
                $data = Normalizer::normalize($this->request->getPayload(), Event::getNamespace());
                $Calendar = new CalendarRepository($this->params);
                $resource_id = $Calendar->save($data);

                // debug
                $CalendarRepository = new CalendarRepository($this->params);
                $object = $CalendarRepository->getById($resource_id, new Event);
                $res = Normalizer::denormalize($object);
                Response::ok($res);
                // Response::noContent();
            case "DELETE":
                Response::ok("Deleteing not implemented yet");
            default:
                Response::methodNotAllowed();
        }
    }
}
