<?php

declare(strict_types=1);

namespace Lea\Core\Repository;

use Lea\Core\Database\DatabaseManager;
use Lea\Module\Security\Service\AuthorizedUserService;
use Lea\Response\Response;

abstract class Repository extends DatabaseManager implements RepositoryInterface
{
    public function __construct(object $object = null)
    {
        $this->object = $this->getObjectInstance();
        if ($object && $object->getNamespace() != $this->object)
            Response::internalServerError("Object doesn't match repository");
        $user_id = AuthorizedUserService::getAuthorizedUserId();
        parent::__construct($this->object, $user_id);
    }

    private function getObjectInstance(): object
    {
        $namespace = get_called_class();
        $namespace = str_replace("\Repository", "\Entity", $namespace);
        $namespace = str_replace("Repository", "", $namespace);

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
        $res = $this->getRecordData($this->getObjectInstance(), $id);
        return $res;
    }

    public function getByField(string $field_name, $field_value)
    {
        $res = $this->getRecordData($this->object, $field_value, $field_name);

        return $res;
    }

    public function updateById(object $object, int $id)
    {
        $object->setId($id);
        $affected_rows = $this->save($object);

        return $affected_rows;
    }

    public function getList()
    {
        $result = $this->getRecordsData($this->object);

        return $result;
    }

    public function getListByField($field_name, $field_value)
    {
        $result = $this->getRecordsData($this->object, $field_value, $field_name);

        return $result;
    }

    public function removeById(int $id): void
    {
        $this->removeRecordData($this->object, $id);
    }
}
