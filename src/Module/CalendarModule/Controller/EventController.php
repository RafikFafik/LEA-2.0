<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\CalendarModule\Entity\Event;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Entity\Calendar;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Module\CalendarModule\Repository\CalendarRepository;

class EventController implements ControllerInterface
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
                    Response::badRequest();
                }



            case "DELETE":
                Response::notImplemented();
            default:
                Response::methodNotAllowed();
        }
    }
}
