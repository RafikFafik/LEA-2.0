<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Response\Response;

class CalendarPlaygroundController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        Response::ok($this->request->getPayload());
    }
}
