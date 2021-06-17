<?php

namespace Lea\Core\Repository;

use Lea\Core\Database\DatabaseManager;
use Lea\Module\OfferModule\Entity\Offer;

abstract class Repository extends DatabaseManager
{
    public function __construct()
    {
        $this->object = $this->getEntityName();
        $user = "xdd";
        parent::__construct(new $this->object, $user);
    }

    private function getEntityName(): string
    {
        $namespace = get_called_class();
        $namespace = str_replace("\Repository", "\Entity", $namespace);
        $namespace = str_replace("Repository", "", $namespace);

        return $namespace;
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
