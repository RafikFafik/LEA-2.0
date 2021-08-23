<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Module\CalendarModule\Entity\CalendarEvent;
use Lea\Module\CalendarModule\Repository\CalendarEventRepository;

class CalendarEventController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $CalendarRepository = new CalendarEventRepository;
                    $object = $CalendarRepository->findById($this->params['id']);
                    $res = Normalizer::denormalize($object);
                    Response::ok($res);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                break;
            case "POST":
            case "PUT":
                try {
                    $repository = new CalendarEventRepository;
                    $object = Normalizer::normalize($this->request->getPayload(), CalendarEvent::getNamespace());
                    $repository->updateById($object, $this->params['id']);
                    Response::noContent();
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest("Resource does not exists");
                }
                break;
            case "DELETE":
                $eventRepository = new CalendarEventRepository;
                $eventRepository->removeById($this->params['id']);
                Response::noContent();
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
