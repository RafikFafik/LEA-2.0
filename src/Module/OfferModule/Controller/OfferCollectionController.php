<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\OfferModule\Entity\Offer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\OfferModule\Repository\OfferRepository;

class OfferCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                $offer_repository = new OfferRepository();
                $list = $offer_repository->findList();
                $res = Normalizer::denormalizeList($list);
                Response::ok($res);
            case "POST":
                $object = Normalizer::normalize($this->request->getPayload(), Offer::getNamespace());
                $repository = new OfferRepository();
                $id = $repository->save($object);
                $res = $repository->findById($id, new Offer);
                $res = Normalizer::denormalize($res);
                Response::ok($res);
            default:
                Response::methodNotAllowed();
        }
    }
}
