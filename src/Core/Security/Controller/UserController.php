<?php

declare(strict_types=1);

namespace Lea\Core\Security\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Security\Entity\User;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Core\Exception\ResourceNotExistsException;


class UserController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $repository = new UserRepository();
                    $object = $repository->findById($this->params['id'], new User);
                    $result = Normalizer::denormalize($object);
                    Response::ok($result);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                break;
            case "POST":
            case "PUT":
                try {
                    $repository = new UserRepository();
                    $payload = $this->request->getPayload();
                    unset($payload['email']);
                    $object = Normalizer::normalize($payload, User::getNamespace());
                    if($object->getEmail())
                        Response::badRequest("Cannot change email");
                    $affected_rows = $repository->updateById($object, $this->params['id']);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                $object = $repository->findById($this->params['id'], new User);
                $result = Normalizer::denormalize($object);
                Response::ok($result);
                break;
               
            case "DELETE":
                $repository = new UserRepository();
                $repository->removeById($this->params['id']);
                Response::noContent();
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
