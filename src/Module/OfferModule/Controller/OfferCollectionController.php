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
                $offer_repository = new OfferRepository();
                $list = $offer_repository->getList(new Offer);
                $res = Normalizer::denormalizeList($list);
                Response::ok($res);
            case "POST":
                $object = Normalizer::normalize($this->request->getPayload(), Offer::getNamespace());
                $offer_repository = new OfferRepository();
                $id = $offer_repository->save($object);
                $res = $offer_repository->getById($id, new Offer);
                $res = Normalizer::denormalize($res);
                Response::ok($res);
            default:
                Response::methodNotAllowed();
        }
    }
}
