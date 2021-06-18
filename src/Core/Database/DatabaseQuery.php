<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Lea\Core\Reflection\Reflection;

final class DatabaseQuery extends DatabaseUtil
{
    public static function getSelectRecordDataQuery(object $object, string $tableName, string $columns, $where_val = null, $where_column = "id"): string
    {
        $query = "SELECT $columns ";
        $query .= "FROM " . $tableName . " ";
        $query .= "WHERE `fld_Deleted` = 0";
        if ($where_val) {
            $type = gettype($where_val);
            if ($type == "string")
                $where_val = "'" . $where_val . "'";
            if ($where_column)
                $query .=  ' AND ' . self::convertKeyToColumn($where_column) . ' = ' .  $where_val;
        }
        if($where_column)
            $query .= ';';

        return $query;
    }

    public static function getQueryWithConstraints(object $object, string $columns, array $constraints): string
    {
        $table_name = self::getTableNameByObject($object);
        $query = self::getSelectRecordDataQuery($object, $table_name, $columns, null, null);
        foreach ($constraints as $key => $val) {
            if (str_contains($key, "_IN") && $object->hasKey(substr($key, 0, strpos($key, "_IN")))) {
                $query .= " AND " . self::convertKeyToColumn(substr($key, 0, strpos($key, "_IN"))) . " IN ('" . join("','", $val) . "')";
            } elseif (str_contains($key, "_LIKE") && $object->hasKey(substr($key, 0, strpos($key, "_LIKE")))) {
                $query .= " AND " . self::convertKeyToColumn(substr($key, 0, strpos($key, "_LIKE"))) . " LIKE '%" . $val . "%'";
            } elseif ($object->hasKey($key)) {
                $query .= " AND " . self::convertKeyToColumn($key) . "='" . $val . "'";
            }
        }

        return $query;
    }

    public static function getInsertIntoQuery(object $object, string $parent_class = NULL, int $parent_id = NULL): string
    {
        $table_name = self::getTableNameByObject($object);
        $columns = "";
        $values = "";
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

            if (gettype($value) == "string" || $property->getType2() == "Date")
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
            if (gettype($value) == "string" || $property->getType2() == "Date")
                $value = "'" . $value . "'";
            elseif (gettype($value) == "boolean")
                $value = (int)$value;

            $changes .= self::convertKeyToColumn($var) . ' = ' . $value . ', ';
        }
        $changes = rtrim($changes, ', ');
        $query = 'UPDATE ' . $table_name .
            ' SET ' . $changes .
            ' WHERE ' . self::convertKeyToColumn($where_column) . " = " . $where_val;
        // TODO - Deleted 0 - verify
        if ($parent_where_val && $parent_key)
            $query .= ' AND ' . self::convertKeyToColumn($parent_key) . " = " . $parent_where_val;

        return $query;
    }

    public static function getCountQuery(object $object, $where_val, string $where_column): string
    {
        $table_name = self::getTableNameByObject($object);
        $query = 'SELECT COUNT(*) AS `count` FROM ' . $table_name . ' WHERE ' . self::convertKeyToColumn($where_column) . ' = ' . $where_val;
        $query .= ' AND `fld_Deleted` = 0';
        return $query;
    }

    public static function getSoftDeleteQuery(object $object, $where_val): string
    {
        $table_name = self::getTableNameByObject($object);
        $query = 'UPDATE ' . $table_name . ' SET `fld_Deleted` = 1 WHERE `fld_Id` = ' . $where_val;

        return $query;
    }
}
