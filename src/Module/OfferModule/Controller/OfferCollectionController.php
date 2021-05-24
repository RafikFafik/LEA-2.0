<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Core\Controller\ControllerInterface;
use Lea\Module\OfferModule\Repository\OfferRepository;
use Lea\Request\Request;
use Lea\Response\Response;
class OfferCollectionController implements ControllerInterface {

    function __construct(Request $request, array $params = NULL) {
        $this->request = $request;
        $this->params = $params;
    }

    public function init() {
        switch($this->request->method()) {
            case "GET":
                $offer = new OfferRepository();
                $offer->getById($this->params['id']);
            case "POST":
                exit;
            default:
                Response::methodNotAllowed();
        }
    }
}
