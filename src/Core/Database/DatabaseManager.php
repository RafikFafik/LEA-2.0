<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use RegexIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Lea\Core\Validator\TypeValidator;
use Lea\Core\Reflection\ReflectionClass;
use Lea\Core\Validator\NamespaceValidator;
use Lea\Core\Reflection\ReflectionProperty;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Exception\UpdatingNotExistingResource;
use Lea\Core\Exception\RemovingResourceThatHasReferenceException;

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
                $this->root_object->$setVal(TypeValidator::getTypedValue($val, $property, TypeValidator::DATABASE));
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
                        $object->$setVal(TypeValidator::getTypedValue($val, $property, TypeValidator::DATABASE));
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
        $this->updateProtection($object, [$where_column => $where_value], $tableName, $columns);
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
    public const NOT_EXISTS = 0;
    public const EXISTS = 1;
    private function updateProtection(object $object, array $constraints, $tableName, $columns, $check_for_empty = self::NOT_EXISTS): void
    {
        $query = $this->query_provider->getCountQuery($object, $constraints);
        $result = DatabaseConnection::executeQuery($query, $tableName, $columns, $object);
        $row = mysqli_fetch_assoc($result);
        $table = $object->getClassName();
        if ($check_for_empty == self::NOT_EXISTS && $row['count'] == 0)
            throw new UpdatingNotExistingResource("Table: $table");
        elseif ($check_for_empty == self::EXISTS && $row['count'] > 0)
            throw new RemovingResourceThatHasReferenceException("Main Table: $table, constraint failed: " .  json_encode($constraints, JSON_PRETTY_PRINT));
    }

    protected function removeRecordData(object $object, $constraints)
    {
        $query = $this->query_provider->getSoftDeleteQuery($object, $constraints['id']);
        $tableName = KeyFormatter::getTableNameByObject($object);
        $columns = KeyFormatter::getTableColumnsByObject($object);
        $this->updateProtection($object, $constraints, $tableName, $columns, self::NOT_EXISTS);
        $this->checkExistingReferences($object, $constraints);
        DatabaseConnection::executeQuery($query, $tableName, $columns, $object);
    }

    /** Not finished - do not use */
    private function checkExistingReferences(object $needle, $constraints): void
    {
        $needle2 = $needle->getClassName();
        $entity_id = KeyFormatter::convertParentClassToForeignKey($needle2);
        $namespaces = $this->getNamespaces();
        foreach ($namespaces as $registered_class) {
            if (
                !str_contains($registered_class, "Entity") ||
                ($reflector = new ReflectionClass($registered_class))->isAbstract()
            ) {
                continue;
            }
            $checked_object = new $registered_class;
            if ($checked_object->hasKey($entity_id)) {
                $current_constraints = $constraints;
                try {
                    $this->updateProtection(
                        $checked_object,
                        [$entity_id => $current_constraints['id']],
                        KeyFormatter::getTableNameByObject($checked_object),
                        KeyFormatter::getTableColumnsByObject($checked_object),
                        self::EXISTS
                    );
                } catch (RemovingResourceThatHasReferenceException $e) {
                    throw new RemovingResourceThatHasReferenceException("Tried to delete: $needle2, existed constraint: "  . $checked_object->getClassName());
                }
            } else if ($reflector->hasProperty(KeyFormatter::processPascalToSnake($needle2) . 's')) {
                $current_constraints = $constraints;
                $current_constraints[KeyFormatter::convertParentClassToForeignKey($checked_object->getClassName()) . "_NOTNULL"] = null;
                $current_constraints[KeyFormatter::convertParentClassToForeignKey($checked_object->getClassName()) . "_>="] = 1;
                try {
                    $this->updateProtection($needle, $current_constraints, KeyFormatter::getTableNameByObject($checked_object), KeyFormatter::getTableColumnsByObject($checked_object), self::EXISTS);
                } catch (RemovingResourceThatHasReferenceException $e) {
                    throw new RemovingResourceThatHasReferenceException("Tried to delete: $needle2, existed constraint: "  . $checked_object->getClassName());
                }
            }
        }
    }

    private function getNamespaces()
    {
        $path = __DIR__ . '/../../';
        $fqcns = array();

        $allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $phpFiles = new RegexIterator($allFiles, '/\.php$/');
        foreach ($phpFiles as $phpFile) {
            $content = file_get_contents($phpFile->getRealPath());
            $tokens = token_get_all($content);
            $namespace = '';
            for ($index = 0; isset($tokens[$index]); $index++) {
                if (!isset($tokens[$index][0])) {
                    continue;
                }
                if (T_NAMESPACE === $tokens[$index][0]) {
                    $index += 2; // Skip namespace keyword and whitespace
                    while (isset($tokens[$index]) && is_array($tokens[$index])) {
                        $namespace .= $tokens[$index++][1];
                    }
                }
                if (T_CLASS === $tokens[$index][0] && T_WHITESPACE === $tokens[$index + 1][0] && T_STRING === $tokens[$index + 2][0]) {
                    $index += 2; // Skip class keyword and whitespace
                    $fqcns[] = $namespace . '\\' . $tokens[$index][1];

                    # break if you have one class per file (psr-4 compliant)
                    # otherwise you'll need to handle class constants (Foo::class)
                    break;
                }
            }
        }

        return $fqcns;
    }
}
