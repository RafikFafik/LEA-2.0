<?php

namespace Lea\Module\ProductModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ProductModule\Entity\Product;
use Lea\Module\ProductModule\Repository\ProductRepository;

class ProductCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                $repository = new ProductRepository();
                $list = $repository->getList(new Product);
                $res = Normalizer::denormalizeList($list);
                Response::ok($res);
            case "POST":
                $object = Normalizer::normalize($this->request->getPayload(), Product::getNamespace());
                $repository = new ProductRepository();
                $id = $repository->save($object);
                $res = $repository->findById($id, new Product);
                $res = Normalizer::denormalize($res);
                Response::ok($res);
            default:
                Response::methodNotAllowed();
        }
    }
}
