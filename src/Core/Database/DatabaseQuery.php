<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use ReflectionClass;
use ReflectionProperty;

final class DatabaseQuery extends DatabaseUtil
{
    public static function getInsertIntoQuery(object $object): string
    {
        $table_name = self::getTableNameByObject($object);
        $columns = "";
        $values = "";
        $reflection = new ReflectionClass(get_class($object));
        $protected_properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);
        $private_properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);
        foreach ($object->getGetters() as $getValue) {
            $value = $object->$getValue();
            if (is_iterable($value))
                continue;
            $key = str_replace('get', '', $getValue);
            if ($value === NULL)
                continue;
            $columns .= self::convertKeyToColumn($key) . ', ';
            if (gettype($value) == "string")
                $value = "'" . $value . "'";
            elseif(gettype($value) == "bool")
                $value = (int)$value;
                
            $values .= $value . ', ';
        }
        $columns = rtrim($columns, ', ');
        $values = rtrim($values, ', ');
        $query = 'INSERT INTO ' . $table_name . ' (' . $columns . ') VALUES (' . $values . ');';

        return $query;
    }
}
