<?php

declare(strict_types=1);

namespace Lea\Core\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Validator\Validator;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Database\DatabaseConnection;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Security\Service\TokenVerificationService;

abstract class Controller implements ControllerInterface
{
    protected $request;

    public function __construct(Request $request, array $params = NULL, array $allow = NULL)
    {
        $this->request = $request;
        $this->params = $params;
        $this->allow = $allow;
        $this->http_method = $request->method();

        if ($params)
            Validator::validateParams($params);

        DatabaseConnection::establishDatabaseConnection();

        if (in_array("all", $allow))
            return;

        $auth = new TokenVerificationService();
        $this->user = $auth->authorize();
    }

    public function init(): void
    { 
        $Repository = $this->getRepositoryClass();
        $this->repository = new $Repository();

        switch ($this->http_method) {
            case "GET":
                if ($this->isCollectionController())
                    $this->getCollection();
                else
                    $this->getResource();
                break;
            case "POST":
            case "PUT":
                if ($this->isCollectionController())
                    $this->postResource();
                else
                    $this->updateResource();
                break;
            case "DELETE":
                if ($this->isCollectionController())
                    Response::methodNotAllowed();
                else
                    $this->deleteResource();
                break;
            default:
                Response::methodNotAllowed();
        }
    }

    protected function isCollectionController(): bool
    {
        $namespace = get_called_class();

        return str_contains($namespace, "CollectionController");
    }

    protected function getRepositoryClass(): string
    {
        $namespace = get_called_class();
        $namespace = str_replace("\Controller", "\Repository", $namespace);
        if (str_contains($namespace, "\Security"))
            $namespace = str_replace("Core", "Core", $namespace);
        $namespace = str_replace("CollectionController", "Repository", $namespace);
        $namespace = str_replace("Controller", "Repository", $namespace);
        /* TODO - unique replacement */

        return $namespace;
    }

    public function getResource(): void
    {
        try {
            $object = $this->repository->findById($this->params['id']);
            $result = Normalizer::denormalize($object);
            Response::ok($result);
        } catch (ResourceNotExistsException $e) {
            Response::badRequest();
        }
    }
    public function getCollection(): void
    {
        $list = $this->repository->findList();
        $res = Normalizer::denormalizeList($list);
        Response::ok($res);
    }


    public function postResource(): void
    {
        $object = Normalizer::normalize($this->request->getPayload(), $this->repository->getEntityClass());
        $id = $this->repository->save($object);
        $result = $this->repository->findById($id);
        $result = Normalizer::denormalize($result);
        Response::ok($result);
    }

    public function updateResource(): void
    {
        try {
            $object = Normalizer::normalize($this->request->getPayload(), $this->repository->getEntityClass());
            $this->repository->updateById($object, $this->params['id']);
            /* DEBUG */
            $this->getResource();
            /* DEBUG */
            Response::noContent();
        } catch (ResourceNotExistsException $e) {
            Response::badRequest();
        }
    }

    public function deleteResource(): void
    {
        $this->repository->removeById($this->params['id']);
        Response::noContent();
    }
}
