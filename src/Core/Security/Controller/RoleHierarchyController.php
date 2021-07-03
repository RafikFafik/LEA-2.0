<?php

declare(strict_types=1);

namespace Lea\Module\Security\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Security\Entity\Role;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Security\Repository\RoleRepository;
use Lea\Core\Exception\ResourceNotExistsException;


class RoleHierarchyController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $repository = new RoleRepository;
                    $object = $repository->findById($this->params['id']);
                    $result = Normalizer::denormalize($object);
                    Response::ok($result);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
