<?php

declare(strict_types=1);

namespace Lea\Core\Security\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Security\Repository\RoleRepository;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\View\ViewGenerator;
use Lea\Response\Response;

final class RoleCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->http_method) {
            case "GET":
                $repository = new RoleRepository();
                $list = $repository->findList();
                Response::ok(Normalizer::denormalizeSpecificFieldsList($list, ['id', 'name', 'role_id']));
            case "POST":
            case "PUT":
            case "DELETE":
                Response::methodNotAllowed();
        }
    }
}
