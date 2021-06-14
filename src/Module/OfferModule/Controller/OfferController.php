<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Module\OfferModule\Entity\Offer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\OfferModule\Repository\OfferRepository;

class OfferController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $offerRepository = new OfferRepository();
                    $object = $offerRepository->getById($this->params['id'], new Offer);
                    $result = Normalizer::denormalize($object);
                    Response::ok($result);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                } finally {
                    Response::badRequest("Coś nie tak");
                }
                break;
            case "POST":
                try {
                    $offerRepository = new OfferRepository($this->params);
                    $object = Normalizer::normalize($this->request->getPayload(), Offer::getNamespace());
                    $affected_rows = $offerRepository->updateById($object, $this->params['id']);
                    Response::noContent();
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                break;
            case "PUT":
                try {
                    $offerRepository = new OfferRepository($this->params);
                    $object = Normalizer::normalize($this->request->getPayload(), Offer::getNamespace());
                    $affected_rows = $offerRepository->updateById($object, $this->params['id']);
                    Response::noContent();
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                break;
            case "DELETE":
                try {
                    $offerRepository = new OfferRepository;
                    $offerRepository->removeById(new Offer, $this->params['id']);
                    Response::noContent();
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
