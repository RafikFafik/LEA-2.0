<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use ReflectionClass;
use Lea\Core\Reflection\Reflection;

final class DatabaseQuery extends DatabaseUtil
{
    public static function getSelectRecordDataQuery(object $object, string $tableName, string $columns, $fldVal, $fldName = "id"): string
    {
        $query = "SELECT $columns ";
        $query .= "FROM " . $tableName . " ";
        $query .= "WHERE " . self::convertKeyToColumn($fldName) . "='" . $fldVal . "' AND `fld_Deleted` = 0";

        return $query;
    }

    public static function getInsertIntoQuery(object $object): string
    {
        $table_name = self::getTableNameByObject($object);
        $columns = "";
        $values = "";
        $class = get_class($object);
        $reflection = new ReflectionClass($class);
        $protected_properties = $reflection->getProperties(Reflection::IS_PROTECTED);
        $private_properties = $reflection->getProperties(Reflection::IS_PRIVATE);
        $properties = array_merge($protected_properties, $private_properties);
        foreach ($properties as $var) {
            $var = $var->getName();
            $getValue = 'get' . self::processSnakeToPascal($var);
            $reflection = new Reflection($class, $var);
            $value = $object->$getValue();
            if (is_iterable($value))
                continue;
            if ($value === NULL)
                continue;
            $columns .= self::convertKeyToColumn($var) . ', ';

            if (gettype($value) == "string")
                $value = "'" . $value . "'";
            elseif (gettype($value) == "boolean")
                $value = (int)$value;

            $values .= $value . ', ';
        }
        $columns = rtrim($columns, ', ');
        $values = rtrim($values, ', ');
        $query = 'INSERT INTO ' . $table_name . ' (' . $columns . ') VALUES (' . $values . ');';

        return $query;
    }
}
