<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Lea\Core\Reflection\ReflectionClass;
use Lea\Core\Validator\NamespaceValidator;
use Lea\Core\Reflection\ReflectionProperty;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Exception\UpdatingNotExistingResource;

abstract class DatabaseManager
{
    const PROPERTY = 2;
    const METHOD = 1;
    const VALUE = 0;

    private $root_object;
    private $root_reflector;
    private $tableName;
    /**
     * @var QueryProvider
     */
    private $query_provider;

    protected function __construct(object $object, $user_id = null)
    {
        $this->tableName = NamespaceValidator::isViewEntity($object) ? KeyFormatter::getViewNameByClass($object->getClassName()) : KeyFormatter::getTableNameByObject($object);
        $this->query_provider = new QueryProvider($this->tableName);
        $this->root_object = $object;
        $this->root_reflector = new ReflectionClass($object);
        if ($user_id)
            $this->user_id = $user_id;
    }

    protected function getRecordData($where_value, $where_column = "id"): object
    {
        return $this->getObjectData($where_value, $where_column, false);
    }

    protected function getNestedRecordData($where_value, $where_column = "id"): object
    {
        return $this->getObjectData($where_value, $where_column, true);
    }

    private function getObjectData($where_value, $where_column = "id", $is_nested): object
    {
        $columns = KeyFormatter::getTableColumnsByReflector($this->root_reflector);
        $query = $this->query_provider->getSelectRecordDataQuery($this->tableName, $columns, $where_value, $where_column);

        $result = DatabaseConnection::executeQuery($query, $this->tableName, $columns, $this->root_object);
        if ($result->num_rows == 0)
            throw new ResourceNotExistsException($this->root_object->getClassName() . ": $where_value");

        $row = mysqli_fetch_assoc($result);
        foreach ($row as $key => $val) {
            if ($val === null)
                continue;
            $key = KeyFormatter::convertToKey($key);
            $setVal = 'set' . KeyFormatter::processSnakeToPascal($key);
            $property = new ReflectionProperty(get_class($this->root_object), $key, $this->root_reflector->getNamespaceName());
            if (method_exists($this->root_object, $setVal) && $property->isObject()) {
                $children[] = $setVal;
            } elseif (method_exists($this->root_object, $setVal)) {
                $type = $property->getType2();
                $this->root_object->$setVal(KeyFormatter::castVariable($val, $type));
            }
        }
        if ($is_nested)
            $this->includeNestedObjectData();

        return $this->root_object;
    }

    private function includeNestedObjectData(): void
    {
        $properties = $this->root_reflector->getObjectProperties();
        $constraints = [KeyFormatter::convertParentClassToForeignKey($this->root_object->getClassName()) => $this->root_object->getId()];
        foreach ($properties as $property) {
            $key = $property->getName();
            $setVal = 'set' . KeyFormatter::processSnakeToPascal($key);
            $child_object_name = $property->getType2();
            $child_object = new $child_object_name;
            /* TODO - Currently - Get Record by record -> Get multiple records at once */
            $children_objects = $this->getListDataByConstraints($child_object, $constraints);
            $this->root_object->$setVal($children_objects);
        }
    }

    protected function getListDataByConstraints(object $object, $constraints = [], $pagination = null, $nested = true)
    {
        $reflector = new ReflectionClass($object);
        $tableName = NamespaceValidator::isViewEntity($object) ? KeyFormatter::getViewNameByClass($object->getClassName()) : KeyFormatter::getTableNameByObject($object);
        $columns = KeyFormatter::getTableColumnsByReflector($reflector);
        $query = $this->query_provider->getQueryWithConstraints($object, $columns, $constraints, $pagination, $reflector, $tableName);

        $result = DatabaseConnection::executeQuery($query, $tableName, $columns, $object);
        if ($result) {
            $Class = get_class($object);
            while ($row = mysqli_fetch_assoc($result)) {
                $object = new $Class;
                foreach ($row as $key => $val) {
                    if ($val === null)
                        continue;
                    $key = KeyFormatter::convertToKey($key);
                    $setVal = 'set' . KeyFormatter::processSnakeToPascal($key);
                    $property = new ReflectionProperty(get_class($object), $key);
                    if (method_exists($object, $setVal) && $property->isObject() && $nested) {
                        $children[] = $setVal; /* TODO - Nested Objects */
                    } elseif (method_exists($object, $setVal)) {
                        $type = $property->getType2();
                        $object->$setVal(KeyFormatter::castVariable($val, $type));
                    }
                }
                if ($nested) {
                    foreach ($reflector->getObjectProperties() as $property) {
                        $key = $property->getName();
                        $setVal = 'set' . KeyFormatter::processSnakeToPascal($key);
                        $child_object_name = $property->getType2();
                        $child_object = new $child_object_name;
                        /* TODO - Currently - Get Record by record -> Get multiple records at once */
                        $children_objects = $this->getListDataByConstraints($child_object, [KeyFormatter::convertParentClassToForeignKey($object->getClassName()) => $object->getId()]);
                        $object->$setVal($children_objects);
                    }
                }
                $objects[] = $object;
            }
        }

        return $objects ?? [];
    }

    public function getCountData(): int
    {
        $query = $this->query_provider->getCountQuery($this->object);
        $result = DatabaseConnection::executeQuery($query, $this->tableName);
        $row = mysqli_fetch_assoc($result);

        return (int)$row['count'];
    }

    protected function insertRecordData(object $object, string $parent_class = NULL, $parent_id = NULL)
    {
        $query = $this->query_provider->getInsertIntoQuery($object, $parent_class, $parent_id);
        $tableName = KeyFormatter::getTableNameByObject($object);
        $columns = KeyFormatter::getTableColumnsByObject($object);

        DatabaseConnection::executeQuery($query, $tableName, $columns, $object, $parent_class);
        $id = DatabaseConnection::getInsertId();
        $child_objects = $object->getChildObjects();
        $class = $object->getClassName();
        $this->insertOrUpdateOrDeleteIterablyChildrenObjects($child_objects, $class, $id);

        return $id;
    }

    protected function updateData(object $object, $where_value, $where_column = "id", $parent_id = NULL, $parent_key = NULL)
    {
        $query = $this->query_provider->getUpdateQuery($object, $where_value, $where_column, $parent_id, $parent_key);
        $tableName = KeyFormatter::getTableNameByObject($object);
        $columns = KeyFormatter::getTableColumnsByObject($object);
        $this->updateProtection($object, $where_value, $where_column, $tableName, $columns);
        $result = DatabaseConnection::executeQuery($query, $tableName, $columns, $object);
        $affected_rows = DatabaseConnection::getAffectedRows();
        $child_objects = $object->getChildObjects();
        if (!$child_objects)
            return;
        $class = $object->getClassName();
        $id = $object->getId();
        $this->insertOrUpdateOrDeleteIterablyChildrenObjects($child_objects, $class, $id);

        return $affected_rows;
    }

    private function insertOrUpdateOrDeleteIterablyChildrenObjects(iterable $iterables, string $parent_class, int $parent_id)
    {
        foreach ($iterables as $iterable) {
            foreach ($iterable ?? [] as $obj) {
                if ($obj->hasId())
                    $this->updateData($obj, $obj->getId(), "id", $parent_id, KeyFormatter::convertParentClassToForeignKey($parent_class));
                else
                    $this->insertRecordData($obj, $parent_class, $parent_id);
            }
        }
    }

    private function updateProtection(object $object, $where_value, $where_column, $tableName, $columns): void
    {
        $query = $this->query_provider->getCountQuery($object, $where_value, $where_column);
        $result = DatabaseConnection::executeQuery($query, $tableName, $columns, $object);
        $row = mysqli_fetch_assoc($result);
        $table = $object->getClassName();
        if ($row['count'] == 0)
            throw new UpdatingNotExistingResource("Table: $table");
    }

    protected function removeRecordData(object $object, $where_value)
    {
        $query = $this->query_provider->getSoftDeleteQuery($object, $where_value);
        $tableName = KeyFormatter::getTableNameByObject($object);
        $columns = KeyFormatter::getTableColumnsByObject($object);
        $this->updateProtection($object, $where_value, 'id', $tableName, $columns);
        DatabaseConnection::executeQuery($query, $tableName, $columns, $object);
    }
}
