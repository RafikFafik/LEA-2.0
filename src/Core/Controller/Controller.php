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
use Lea\Core\Repository\Repository;
use Lea\Core\Security\Service\TokenVerificationService;

abstract class Controller implements ControllerInterface
{
    protected $request;
    /**
     * @var array|null
     */
    protected $config;
    protected $repository;
    protected $http_method;

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
                    $view = new ViewGenerator($repository);
                    $result = $view->getView();
                    Response::ok($result);
                } else {
                    $Repository = $this->getRepositoryClass();
                    $repository = new $Repository();
                    $object = $this->getResource($repository);
                    $result = Normalizer::denormalize($object);
                    Response::ok($result);
                }
                break;
            case "POST":
            case "PUT":
                if ($this->isCollectionController()) {
                    $Repository = $this->getRepositoryClass();
                    $repository = new $Repository();
                    $this->postResource($repository);
                } else {
                    $Repository = $this->getRepositoryClass();
                    $repository = new $Repository();
                    $this->updateResource($repository);
                }
                break;
            case "DELETE":
                if ($this->isCollectionController()) {
                    Response::methodNotAllowed();
                } else {
                    $Repository = $this->getRepositoryClass();
                    $repository = new $Repository();
                    $this->deleteResource($repository);
                }
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

    public function getResource(Repository $repository): object
    {
        try {
            $object = $repository->findById($this->params['id']);

            return $object;
        } catch (ResourceNotExistsException $e) {
            Response::badRequest();
        }
    }

    public function getCollection(): iterable
    {
        return $this->repository->findList();
    }

    public function postResource(Repository $repository): void
    {
        $object = Normalizer::normalize($this->request->getPayload(), $repository->getEntityClass());
        $id = $repository->save($object);
        $result = $repository->findById($id);
        $result = Normalizer::denormalize($result);
        Response::ok($result);
    }

    public function updateResource(Repository $repository): void
    {
        try {
            $object = Normalizer::normalize($this->request->getPayload(), $repository->getEntityClass());
            $repository->updateById($object, $this->params['id']);
            /* DEBUG */
            $object = $this->getResource($repository);
            $result = Normalizer::denormalize($object);
            /* DEBUG */
            Response::noContent($result);
        } catch (ResourceNotExistsException $e) {
            Response::badRequest();
        }
    }

    public function deleteResource(Repository $repository): void
    {
        $repository->removeById($this->params['id']);
        Response::noContent();
    }
}
