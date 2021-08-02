<?php

declare(strict_types=1);

namespace Lea\Core\Repository;

use Error;
use Lea\Request\Request;
use Lea\Core\Database\DatabaseManager;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Exception\ViewNotImplementedException;
use Lea\Core\Security\Service\AuthorizedUserService;

abstract class Repository extends DatabaseManager implements RepositoryInterface
{
    /**
     * @var object
     */
    protected $object;

    public function __construct($is_view = false)
    {
        $this->object = $is_view ? $this->getViewInstance() :  $this->getObjectInstance();
        $user_id = AuthorizedUserService::getAuthorizedUserId();
        parent::__construct($this->object, $user_id);
    }

    public function getEntityClass(): string
    {
        return $this->entity_class;
    }

    private function getObjectInstance(): object
    {
        $namespace = get_called_class();
        $namespace = str_replace("\Repository", "\Entity", $namespace);
        $namespace = str_replace("Repository", "", $namespace);
        $this->entity_class = $namespace;

        return new $namespace;
    }

    private function getViewInstance(): object
    {
        $namespace = get_called_class();
        $namespace = str_replace("\Repository", "\View", $namespace);
        $namespace = str_replace("Repository", "", $namespace);
        $this->entity_class = $namespace;

        try {
            return new $namespace;
        } catch (Error $e) {
            throw new ViewNotImplementedException($namespace);
        }
    }

    public function save(object &$object): int
    {
        $object->saveFiles();
        if ($object->hasId())
            $id = $this->updateData($object, $object->getId());
        else
            $id = $this->insertRecordData($object);

        return (int)$id;
    }

    public function findById(int $id)
    {
        return $this->getNestedRecordData($id);
    }

    public function findListByIds(array $ids)
    {
        $constraints = ['id_IN' => $ids];

        return $this->getListDataByConstraints($this->object, $constraints);
    }


    public function updateById(object $object, int $id)
    {
        $object->setId($id);
        $affected_rows = $this->save($object);

        return $affected_rows;
    }

    public function findList(array $constraints = ['active' => true], bool $nested = true)
    {
        $pagination = Request::getPaginationParams();
        $constraints = array_merge(Request::getFilterParams(), $constraints);
        if (isset($this))
            $result = $this->getListDataByConstraints($this->object, $constraints, $pagination, $nested);

        return $result;
    }

    public function findFlatList(array $constraints = [])
    {
        $pagination = Request::getPaginationParams();
        $constraints = array_merge($constraints, Request::getFilterParams());
        $res = $this->getListDataByConstraints($this->object, $constraints, $pagination, false);

        return $res;
    }

    public function removeById(int $id): void
    {
        $this->removeRecordData($this->object, $id);
    }

    public function findCountData(): int
    {
        $result = $this->getCountData();

        return $result;
    }

    public function isUnique($constraints): bool
    {
        try {
            $this->getRecordData($constraints[array_key_first($constraints)], array_key_first($constraints));
        } catch(ResourceNotExistsException $e) {
            return true;
        }

        return false;
    }
}
