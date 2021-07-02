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
            case "PUT":
                $data = Normalizer::normalize($this->request->getPayload(), CalendarEvent::getNamespace());
                $repository = new CalendarEventRepository;
                $repository->save($data);
                
                Response::noContent();
            default:
                Response::methodNotAllowed();
        }
    }
}
