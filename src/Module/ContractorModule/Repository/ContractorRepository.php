<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Core\Database\DatabaseManager;
use Lea\Core\Repository\RepositoryInterface;
use Lea\Module\ContractorModule\Entity\Contractor;

final class ContractorRepository extends DatabaseManager implements RepositoryInterface
{
    private $entity;

    public function __construct(array $params)
    {
        $this->entity = new Contractor();
        parent::__construct($this->entity);
    }

    public static function getById(int $id)
    {
        $res = self::getRecordData(new Contractor, $id);

        return $res;
    }

    public function updateById(object $object, int $id)
    {
        $object->setId($id);
        $affected_rows = $this->save($object);

        return $affected_rows;
    }

    public function save(object $object)
    {
        if($object->hasId())
            $id = $this->updateData($object, $object->getId());
        else
            $id = $this->insertRecordData($object);

        return $id;
    }
}
