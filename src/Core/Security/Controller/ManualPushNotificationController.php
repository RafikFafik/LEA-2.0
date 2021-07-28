<?php

declare(strict_types=1);

namespace Lea\Core\Security\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\CalendarModule\Cron\AlertCron;
use Lea\Response\Response;

class ManualPushNotificationController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                AlertCron::sendAlerts();
                Response::noContent();
            default:
                Response::methodNotAllowed();
        }
    }
}
