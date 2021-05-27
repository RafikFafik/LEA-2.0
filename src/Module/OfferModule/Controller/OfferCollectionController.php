<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\OfferModule\Entity\Offer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\OfferModule\Repository\OfferRepository;

class OfferCollectionController implements ControllerInterface
{

    function __construct(Request $request, array $params = NULL)
    {
        $this->request = $request;
        $this->params = $params;
    }

    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                $offer = new OfferRepository();
                $offer->getById($this->params['id']);
            case "POST":
                $data = Normalizer::normalize($this->request->getPayload(), Offer::getNamespace());
                $offer = new OfferRepository();
                Response::noContent();
            default:
                Response::methodNotAllowed();
        }
    }
}
