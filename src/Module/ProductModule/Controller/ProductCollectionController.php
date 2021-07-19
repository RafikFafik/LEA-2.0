<?php

namespace Lea\Module\ProductModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ProductModule\Repository\ProductRepository;

class ProductCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch($this->http_method) {
            case "GET":
                $repository = new ProductRepository();
        }
    }
}
