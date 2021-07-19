<?php

declare(strict_types=1);

namespace Lea\Core\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\View\ViewGenerator;
use Lea\Core\Validator\Validator;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Database\DatabaseConnection;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Security\Service\TokenVerificationService;

abstract class Controller implements ControllerInterface
{
    protected $request;

    public function __construct(Request $request, array $params = NULL, array $allow = NULL, array $config = null)
    {
        if ($params)
            Validator::validateParams($params);

        $this->request = $request;
        $this->config = $config;
        $this->params = $params;
        $this->allow = $allow;
        $this->http_method = $request->method();

        DatabaseConnection::establishDatabaseConnection();

        if (in_array("all", $allow))
            return;

        $auth = new TokenVerificationService();
        $this->user = $auth->authorize();
    }

    public function init(): void
    {
        switch ($this->http_method) {
            case "GET":
                if ($this->isCollectionController()) {
                    $Repository = $this->getRepositoryClass();
                    $repository = new $Repository(true);
                    $view = new ViewGenerator($repository, $this->pagination);
                    $result = $view->getView($this->repository);
                    Response::ok($result);
                } else {
                    $object = $this->getResource();
                    $result = Normalizer::denormalize($object);
                    Response::ok($result);
                }
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

    private function isCollectionController(): bool
    {
        $namespace = get_called_class();

        return str_contains($namespace, "CollectionController");
    }

    private function getRepositoryClass(): string
    {
        $namespace = get_called_class();
        $namespace = str_replace("\Controller", "\Repository", $namespace);
        if (str_contains($namespace, "\Security"))
            $namespace = str_replace("Module", "Core", $namespace);
        $namespace = str_replace("CollectionController", "Repository", $namespace);
        $namespace = str_replace("Controller", "Repository", $namespace);
        /* TODO - unique replacement */

        return $namespace;
    }

    public function getResource(): object
    {
        try {
            $object = $this->repository->findById($this->params['id']);

            return $object;
        } catch (ResourceNotExistsException $e) {
            Response::badRequest();
        }
    }

    public function getCollection(): iterable
    {
        $list = $this->repository->findList();

        return $list;
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
