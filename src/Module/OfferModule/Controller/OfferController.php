<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Core\Controller\ControllerInterface;
use Lea\Request\Request;

class OfferController implements ControllerInterface {

    private $request;

    function __construct(Request $request) {
        $this->request = $request;
    }

    public function init() {
        $action = $this->request->action();
    }
}
