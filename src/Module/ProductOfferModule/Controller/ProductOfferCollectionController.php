<?php

namespace Lea\Module\ProductOfferModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\View\ViewGenerator;
use Lea\Module\ProductOfferModule\Service\ProductOfferService;
use Lea\Module\ProductOfferModule\Repository\ProductOfferRepository;

class ProductOfferCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        $this->repository = new ProductOfferRepository();
        
        switch ($this->http_method) {
            case "GET":
                $repository = new ProductOfferRepository();
                $service = new ProductOfferService(new ProductOfferRepository());
                Response::ok($service->getView());
            case "POST":
                $repository = new ProductOfferRepository();
                $this->postResource($repository);
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
