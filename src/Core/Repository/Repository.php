<?php

namespace Lea\Core\Repository;

use Lea\Core\Database\DatabaseManager;

abstract class Repository extends DatabaseManager
{
    public function save(object $object)
    {
        if ($object->hasId())
            $id = $this->updateData($object, $object->getId());
        else
            $id = $this->insertRecordData($object);

        return $id;
    }

    public static function getById(int $id, object $object)
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

    public static function getList(object $object)
    {
        $res = self::getRecordsData($object);

        return $res;
    }

    public function removeById(object $object, int $id): void
    {
        $this->removeRecordData($object, $id);
    }
}
