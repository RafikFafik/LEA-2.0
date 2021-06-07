<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Module\OfferModule\Entity\Offer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Module\OfferModule\Repository\OfferRepository;

class OfferController implements ControllerInterface
{

    private $request;
    private $methods;

    function __construct(Request $request, array $params = NULL)
    {
        $this->request = $request;
        $this->params = $params;
    }

    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $offerRepository = new OfferRepository();
                    $res = $offerRepository->getById($this->params['id'], new Offer);
                    Response::ok($res);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
            case "POST":
                Response::notImplemented();
            default:
                Response::methodNotAllowed();
        }
    }
}
