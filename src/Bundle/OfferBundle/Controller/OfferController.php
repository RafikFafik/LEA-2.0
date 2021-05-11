<?php

namespace Lea\OfferBundle\Controller;

use Lea\Controller\ControllerInterface;
use Lea\Request\Request;
use Lea\Response\Response;

class OfferController implements ControllerInterface {

    private $request;

    function __construct(Request $request) {
        $this->request = $request;
    }

    public function init() {
        $action = $this->request->action();
        switch($action) {
            case "collection":
                $controller = new OfferCollectionController($this->request);
                break;
            default:
                Response::notFound();
        }
    }
}
