<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Lea\Core\Reflection\Reflection;

class DatabaseQuery extends DatabaseUtil
{
    public function __construct(object $object)
    {
        $this->object = $object;
    }
    public function getSelectRecordDataQuery(string $tableName, string $columns, $where_val = null, $where_column = "id"): string
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
        if ($where_column)
            $query .= ';';

        return $query;
    }

    public function getQueryWithConstraints(object $object, string $columns, array $constraints, array $pagination = null): string
    {
        if(isset($constraints['order']))
            unset($constraints['order']);
        $table_name = self::getTableNameByObject($object);
        $query = $this->getSelectRecordDataQuery($table_name, $columns, null, null);
        foreach ($constraints as $key => $val) {
            if (str_contains($key, "_IN") && $object->hasKey(substr($key, 0, strpos($key, "_IN")))) {
                $query .= " AND " . self::convertKeyToColumn(substr($key, 0, strpos($key, "_IN"))) . " IN ('" . join("','", $val) . "')";
            } elseif (str_contains($key, "_LIKE") && $object->hasKey(substr($key, 0, strpos($key, "_LIKE")))) {
                $query .= " AND " . self::convertKeyToColumn(substr($key, 0, strpos($key, "_LIKE"))) . " LIKE '%" . $val . "%'";
            } elseif (str_contains($key, "_BETWEEN") && $object->hasKey(substr($key, 0, strpos($key, "_BETWEEN")))) {
                $query .= " AND " . self::convertKeyToColumn(substr($key, 0, strpos($key, "_BETWEEN"))) . " BETWEEN '" . $val['from'] . "' AND '" . $val['to']. '\'';
            } elseif (str_contains($key, "_<=") && $object->hasKey(substr($key, 0, strpos($key, "_<=")))) {
                $query .= " AND " . self::convertKeyToColumn(substr($key, 0, strpos($key, "_<="))) . " <= '" . $val . "'";
            } elseif (str_contains($key, "_>=") && $object->hasKey(substr($key, 0, strpos($key, "_>=")))) {
                $query .= " AND " . self::convertKeyToColumn(substr($key, 0, strpos($key, "_>="))) . " >= '" . $val . "'";
            } elseif ($key == "filters" && is_array($val)) {
                foreach($val as $k => $v) {
                    $query .= " AND " . self::convertKeyToColumn($k) . " LIKE '%" . $v . "%'";
                }
            } elseif ($object->hasKey($key)) {
                $query .= " AND " . self::convertKeyToColumn($key) . "='" . $val . "'";
            }
        }
        if ($pagination) {
            $query .= $this->getPaginationQueryConstraints($object, $pagination);
        }

        return $query;
    }

    public function getInsertIntoQuery(object $object, string $parent_class = NULL, int $parent_id = NULL): string
    {
        $table_name = self::getTableNameByObject($object);
        $columns = "";
        $values = "";
        $reflection = new Reflection($object);
        foreach ($reflection->getProperties() as $property) {
            $var = $property->getName();
            $getValue = 'get' . self::processSnakeToPascal($var);
            if (!method_exists(get_class($object), $getValue))
                continue;
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
        if ($parent_class && !str_contains($columns, self::convertKeyToReferencedColumn($parent_class))) {
            $columns .= self::convertKeyToReferencedColumn($parent_class);
            $values .= $parent_id;
        } else {
            $columns = rtrim($columns, ', ');
            $values = rtrim($values, ', ');
        }
        $query = 'INSERT INTO ' . $table_name . ' (' . $columns . ') VALUES (' . $values . ');';

        return $query;
    }

    public function getUpdateQuery(object $object, $where_val, string $where_column, $parent_where_val = NULL, string $parent_key = NULL): string
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
            elseif (gettype($value) == "int" || $property->getType2() == "Currency")
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

    public function getCountQuery(object $object, $where_val, string $where_column): string
    {
        $table_name = self::getTableNameByObject($object);
        $query = 'SELECT COUNT(*) AS `count` FROM ' . $table_name . ' WHERE ' . self::convertKeyToColumn($where_column) . ' = ' . $where_val;
        $query .= ' AND `fld_Deleted` = 0';
        return $query;
    }

    public function getSoftDeleteQuery(object $object, $where_val): string
    {
        $table_name = self::getTableNameByObject($object);
        $query = 'UPDATE ' . $table_name . ' SET `fld_Deleted` = 1 WHERE `fld_Id` = ' . $where_val;

        return $query;
    }

    public function getCheckIfTableExistsQuery(string $tablename): string
    {
        $query = 'SHOW TABLES LIKE \'%' . $tablename . '%\'';

        return $query;
    }

    private function getPaginationQueryConstraints(object $object, array $params): ?string
    {
        if($object->hasKey($params['sortby']))
            $query = ' ORDER BY ' . self::convertKeyToColumn($params['sortby']) . " " . $params['order'];

        return $query;
    }
}
