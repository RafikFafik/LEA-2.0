<?php

declare(strict_types=1);

namespace Lea\Module\Security\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\SecurityModule\Entity\User;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Security\Repository\UserRepository;

final class UserCollectionController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                $repository = new UserRepository($this->params);
                $list = $repository->getList(new User);
                $res = Normalizer::denormalizeList($list);
                Response::ok($res);
            case "POST":
                $data = Normalizer::normalize($this->request->getPayload(), User::getNamespace());
                $repository = new UserRepository($this->params);
                $resource_id = $repository->save($data);

                // debug
                $repository = new UserRepository($this->params);
                $object = $repository->findById($resource_id, new User);
                $result = Normalizer::denormalize($object);
                Response::ok($result);
            case "DELETE":
                Response::methodNotAllowed();
            default:
                Response::methodNotAllowed();
        }
    }
}
