<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Core\Controller\ControllerInterface;
use Lea\Request\Request;
use Lea\Response\Response;

class OfferConfirmController implements ControllerInterface {

    private $request;
    private $methods;

    function __construct(Request $request, array $params = NULL) {
        $this->request = $request;
        $this->params = $params;
    }

    public function init() {
        switch($this->request->method()) {
            case "GET":
                exit;
            case "POST":
                exit;
            default:
                Response::methodNotAllowed();
        }
    }
}
