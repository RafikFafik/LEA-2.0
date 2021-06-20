<?php

namespace Lea\Core\Repository;

use Lea\Core\Database\DatabaseManager;
use Lea\Module\Security\Service\AuthorizedUserService;
use Lea\Response\Response;

abstract class Repository extends DatabaseManager
{
    public function __construct(object $object = null)
    {
        $this->object = self::getRepositoryObject();
        if($object && $object->getNamespace() != $this->object)
            Response::internalServerError("Object doesn't match repository");
        $user_id = AuthorizedUserService::getAuthorizedUserId();
        parent::__construct($this->object, $user_id);
    }

    private static function getRepositoryObject(): object
    {
        $namespace = get_called_class();
        $namespace = str_replace("\Repository", "\Entity", $namespace);
        $namespace = str_replace("Repository", "", $namespace);

        return new $namespace;
    }

    public function save(object $object)
    {
        if ($object->hasId())
            $id = $this->updateData($object, $object->getId());
        else
            $id = $this->insertRecordData($object);

        return $id;
    }

    public static function findById(int $id, object $object)
    {
        $res = self::getRecordData($object, $id);
        return $res;
    }

    public static function getByField(object $object, string $field_name, $field_value)
    {
        $res = self::getRecordData($object, $field_value, $field_name);

        return $res;
    }

    public function updateById(object $object, int $id)
    {
        $object->setId($id);
        $affected_rows = $this->save($object);

        return $affected_rows;
    }

    public static function getList()
    {
        $result = self::getRecordsData(self::getRepositoryObject());

        return $result;
    }

    public static function getListByField($field_name, $field_value)
    {
        $result = self::getRecordsData(self::getRepositoryObject(), $field_value, $field_name);

        return $result;
    }

    public function removeById(int $id): void
    {
        $this->removeRecordData($this->object, $id);
    }
}
