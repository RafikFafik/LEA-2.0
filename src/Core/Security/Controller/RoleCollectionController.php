<?php

declare(strict_types=1);

namespace Lea\Module\Security\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Security\Entity\Role;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Security\Repository\RoleRepository;

final class RoleCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                $repository = new RoleRepository();
                $list = $repository->findList();
                $res = Normalizer::denormalizeList($list);
                Response::ok($res);
            case "POST":
                $data = Normalizer::normalize($this->request->getPayload(), Role::getNamespace());
                $repository = new RoleRepository();
                $resource_id = $repository->save($data);

                // debug
                $repository = new RoleRepository();
                $object = $repository->findById($resource_id);
                $result = Normalizer::denormalize($object);
                Response::ok($result);
            default:
                Response::methodNotAllowed();
        }
    }
}
