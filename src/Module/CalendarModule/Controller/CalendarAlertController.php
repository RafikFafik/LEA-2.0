<?php

declare(strict_types=1);

namespace Lea\Module\CalendarModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Module\CalendarModule\Cron\AlertCron;
use Lea\Response\Response;

class CalendarAlertController extends Controller
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                AlertCron::sendAlerts();
                Response::ok();
                break;
        }
    }
}
