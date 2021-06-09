<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Request\Request;
use Lea\Response\Response;

class OfferConfirmController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                exit;
            case "POST":
                exit;
            default:
                Response::methodNotAllowed();
        }
    }
}
