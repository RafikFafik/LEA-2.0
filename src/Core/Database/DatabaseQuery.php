<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Lea\Core\Reflection\Reflection;

final class DatabaseQuery extends DatabaseUtil
{
    public static function getSelectRecordDataQuery(object $object, string $tableName, string $columns, $where_val = null, string $where_column = "id"): string
    {
        $query = "SELECT $columns ";
        $query .= "FROM " . $tableName . " ";
        $query .= "WHERE `fld_Deleted` = 0";
        if($where_val)
            $query .= ' AND ' . self::convertKeyToColumn($where_column) . ' = ' .  $where_val;
        $query .= ';';

        return $query;
    }

    public static function getInsertIntoQuery(object $object, string $parent_class = NULL, int $parent_id = NULL): string
    {
        $table_name = self::getTableNameByObject($object);
        $columns = "";
        $values = "";
        $class = get_class($object);
        $reflection = new Reflection($object);
        foreach ($reflection->getProperties() as $property) {
            $var = $property->getName();
            $getValue = 'get' . self::processSnakeToPascal($var);
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
        if ($parent_class) {
            $columns .= self::convertKeyToReferencedColumn($parent_class);
            $values .= $parent_id;
        } else {
            $columns = rtrim($columns, ', ');
            $values = rtrim($values, ', ');
        }
        $query = 'INSERT INTO ' . $table_name . ' (' . $columns . ') VALUES (' . $values . ');';

        return $query;
    }

    public static function getUpdateQuery(object $object, $where_val, string $where_column, $parent_where_val = NULL, string $parent_key = NULL): string
    {
        $table_name = self::getTableNameByObject($object);
        $changes = "";
        $reflection = new Reflection($object);
        foreach ($reflection->getProperties() as $property) {
            $var = $property->getName();
            if ($var == 'id')
                continue;
            $getValue = 'get' . self::processSnakeToPascal($var);
            $value = $object->$getValue();
            if (is_iterable($value))
                continue;
            if ($value === NULL)
                continue;
            if (gettype($value) == "string")
                $value = "'" . $value . "'";
            elseif (gettype($value) == "boolean")
                $value = (int)$value;

            $changes .= self::convertKeyToColumn($var) . ' = ' . $value . ', ';
        }
        $changes = rtrim($changes, ', ');
        $query = 'UPDATE ' . $table_name .
                ' SET ' . $changes . 
                ' WHERE ' . self::convertKeyToColumn($where_column) . " = " . $where_val;
        if($parent_where_val && $parent_key)
            $query .= ' AND ' . self::convertKeyToColumn($parent_key). " = " . $parent_where_val;

        return $query;
    }
}
