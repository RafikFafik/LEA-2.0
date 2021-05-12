<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Core\Controller\ControllerInterface;
use Lea\Request\Request;

class OfferConfirmController implements ControllerInterface {

    private $request;
    private $methods;

    function __construct(Request $request, array $methods, array $params = NULL) {
        $this->request = $request;
        $this->methods = $methods;
        $this->params = $params;
    }

    public function init() {
        die("Tutaj mamy inny endpoint bez parametrów");
    }
}
