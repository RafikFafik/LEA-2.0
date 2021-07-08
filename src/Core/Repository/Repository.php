<?php

declare(strict_types=1);

namespace Lea\Core\Repository;

use Lea\Core\Database\DatabaseManager;
use Lea\Core\Security\Service\AuthorizedUserService;

abstract class Repository extends DatabaseManager implements RepositoryInterface
{
    public function __construct()
    {
        $this->object = $this->getObjectInstance();
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

    public function save(object $object)
    {
        $object->saveFiles();
        if ($object->hasId())
            $id = $this->updateData($object, $object->getId());
        else
            $id = $this->insertRecordData($object);

        return $id;
    }

    public function findById(int $id)
    {
        $res = $this->getNestedRecordData($id);
        return $res;
    }

    public function updateById(object $object, int $id)
    {
        $object->setId($id);
        $affected_rows = $this->save($object);

        return $affected_rows;
    }

    public function findList()
    {
        $constraints = [];
        $result = $this->getListDataByConstraints($this->object, $constraints);

        return $result;
    }

    public function removeById(int $id): void
    {
        $this->removeRecordData($this->object, $id);
    }
}
