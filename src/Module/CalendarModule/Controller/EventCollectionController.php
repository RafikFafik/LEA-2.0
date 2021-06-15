<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\CalendarModule\Entity\Event;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class EventCollectionController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                $CalendarEventRepository = new CalendarEventRepository($this->params);
                $list = $CalendarEventRepository->getList(new Event);
                $res = Normalizer::denormalizeList($list);

                Response::ok($res);
            case "POST":
                $data = Normalizer::normalize($this->request->getPayload(), Event::getNamespace());
                $Calendar = new CalendarEventRepository($this->params);
                $resource_id = $Calendar->save($data);

                // debug
                $CalendarEventRepository = new CalendarEventRepository($this->params);
                $object = $CalendarEventRepository->findById($resource_id, new Event);
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
