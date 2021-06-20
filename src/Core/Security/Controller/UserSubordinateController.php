<?php

declare(strict_types=1);

namespace Lea\Module\Security\Controller;

use Lea\Response\Response;
use Lea\Core\Security\Entity\User;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Module\Security\Service\AuthorizedUserService;
use Lea\Module\Security\Service\UserSubordinateService;


class UserSubordinateController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $role_id = AuthorizedUserService::getAuthorizedUserRoleId();
                    $service = new UserSubordinateService;
                    $objects = $service->findSubordinateUsersRecursive($role_id);
                    $result = Normalizer::denormalizeList($objects);
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
