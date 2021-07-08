<?php

namespace Lea\Module\CalendarModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Response\Response;

use function Lea\Module\CalendarModule\Cron\sendAlerts;

class CalendarAlertController extends Controller {

  public function init() : void {
    switch($this->request->method()) {
      case "GET":
        sendAlerts();
        Response::noContent();
        break;
    }
  }
}
