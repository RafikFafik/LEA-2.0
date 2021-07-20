<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\OfferModule\Repository\OfferRepository;
use Lea\Module\OfferModule\Service\OfferService;
use Lea\Response\Response;

class OfferCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        $this->repository = new OfferRepository();
        $service = new OfferService($this->repository);

        switch ($this->http_method) {
            case "GET":
                Response::ok($service->getView());
            case "POST":
                $id = $service->saveOffer($this->request->getPayload());
                $response = $service->getOfferById($id);
                Response::ok($response);
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
