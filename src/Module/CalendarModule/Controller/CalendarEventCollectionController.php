<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Entity\CalendarEvent;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class CalendarEventCollectionController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                $repository = new CalendarEventRepository;
                $list = $repository->getList(new CalendarEvent);
                $res = Normalizer::denormalizeList($list);

                Response::ok($res);
            case "POST":
                $data = Normalizer::normalize($this->request->getPayload(), CalendarEvent::getNamespace());
                $Calendar = new CalendarEventRepository;
                $resource_id = $Calendar->save($data);

                // debug
                $CalendarEventRepository = new CalendarEventRepository;
                $object = $CalendarEventRepository->findById($resource_id, new CalendarEvent);
                $res = Normalizer::denormalize($object);
                Response::ok($res);
                // Response::noContent();
            default:
                Response::methodNotAllowed();
        }
    }
}
