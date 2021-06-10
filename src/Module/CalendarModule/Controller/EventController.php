<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\CalendarModule\Entity\Event;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Entity\Calendar;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Exception\UpdatingNotExistingResource;
use Lea\Module\CalendarModule\Repository\CalendarRepository;

class EventController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $CalendarRepository = new CalendarRepository($this->params);
                    $object = $CalendarRepository->getById($this->params['id'], new Event);
                    $res = Normalizer::denormalize($object);
                    Response::ok($res);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
            case "POST":
                try {
                    $CalendarRepository = new CalendarRepository($this->params);
                    $object = Normalizer::normalize($this->request->getPayload(), Event::getNamespace());
                    $affected_rows = $CalendarRepository->updateById($object, $this->params['id']);

                    $object = $CalendarRepository->getById($this->params['id'], new Event);
                    $res = Normalizer::denormalize($object);
                    Response::ok($res);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest("Brak zasobu");
                } catch (UpdatingNotExistingResource $e) {
                    Response::badRequest("Próba aktualizacji nieistniejącego zasobu");
                } finally {
                    Response::badRequest("Coś zawiodło");
                }



            case "DELETE":
                $eventRepository = new CalendarRepository($this->params);
                $eventRepository->removeById(new Event(), $this->params['id']);
                Response::noContent();
                Response::notImplemented();
            default:
                Response::methodNotAllowed();
        }
    }
}
