<?php

namespace Lea\Module\ProductOfferModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ProductOfferModule\Service\ProductOfferService;
use Lea\Module\ProductOfferModule\Repository\ProductOfferRepository;

class ProductOfferCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->http_method) {
            case "GET":
                $service = new ProductOfferService(new ProductOfferRepository());
                $list = $service->getView();
                $result = Normalizer::denormalizeList($list);
                $result = Normalizer::removeSpecificFieldsFromArrayList($result, ["products"]);
                Response::ok($result);
            case "POST":
                $this->postResource();
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
