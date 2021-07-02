<?php

namespace Lea\Module\ProductModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ProductModule\Entity\Product;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Module\ProductModule\Repository\ProductRepository;

class ProductController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $repository = new ProductRepository();
                    $object = $repository->findById($this->params['id'], new Product);
                    $result = Normalizer::denormalize($object);
                    Response::ok($result);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                } finally {
                    Response::badRequest("Something went wrong");
                }
                break;
            case "POST":
            case "PUT":
                try {
                    $repository = new ProductRepository();
                    $object = Normalizer::normalize($this->request->getPayload(), Product::getNamespace());
                    $repository->updateById($object, $this->params['id']);
                    Response::noContent();
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                break;
            case "DELETE":
                try {
                    $repository = new ProductRepository();
                    $repository->removeById($this->params['id']);
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
