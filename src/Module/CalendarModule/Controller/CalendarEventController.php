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
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $CalendarRepository = new CalendarEventRepository;
                    $object = $CalendarRepository->findById($this->params['id'], new CalendarEvent);
                    $res = Normalizer::denormalize($object);
                    Response::ok($res);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
            case "POST":
            case "PUT":
                try {
                    $CalendarRepository = new CalendarEventRepository;
                    // $payload = Normalizer::mapKeyOfArrayList($this->request->getPayload(), 'field_id', 'field');
                    $payload = Normalizer::arrayToJson($this->request->getPayload(), 'employees');
                    $object = Normalizer::normalize($payload, CalendarEvent::getNamespace());
                    $affected_rows = $CalendarRepository->updateById($object, $this->params['id']);

                    $object = $CalendarRepository->findById($this->params['id'], new CalendarEvent);
                    $res = Normalizer::denormalize($object);
                    $res = Normalizer::mapKeyOfArrayList($res, 'field', 'field_id');
                    Response::ok($res);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest("Brak zasobu");
                }
            case "DELETE":
                $eventRepository = new CalendarEventRepository;
                $eventRepository->removeById($this->params['id']);
                Response::noContent();
                Response::notImplemented();
            default:
                Response::methodNotAllowed();
        }
    }
}
