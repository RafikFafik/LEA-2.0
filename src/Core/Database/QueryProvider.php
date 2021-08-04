<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Lea\Core\Reflection\ReflectionClass;
use Lea\Core\Validator\NamespaceValidator;

final class QueryProvider
{
    /**
     * @var string
     */
    private $tableName;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
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
                $query .=  ' AND ' . KeyFormatter::convertKeyToColumn($where_column) . ' = ' .  $where_val;
        }
        if ($where_column)
            $query .= ';';

        return $query;
    }

    public function getQueryWithConstraints(object $object, string $columns, array $constraints, array $pagination = null, $reflector = null, $tableName): string
    {
        $constraints = $this->matchCurrencyFields($constraints, $reflector);
        if (isset($constraints['order']))
            unset($constraints['order']);
        $query = $this->getSelectRecordDataQuery($tableName, $columns, null, null);
        $query .= $this->processConstraints($object, $constraints);
        if ($pagination) {
            $query .= $this->getPaginationQueryConstraints($object, $pagination);
        }

        return $query;
    }

    private function processConstraints($object, $constraints): string
    {
        $query = "";
        foreach ($constraints as $key => $val) {
            if (str_contains($key, "_IN") && $object->hasKey(substr($key, 0, strpos($key, "_IN")))) {
                $query .= " AND " . KeyFormatter::convertKeyToColumn(substr($key, 0, strpos($key, "_IN"))) . " IN ('" . join("','", $val) . "')";
            } elseif (str_contains($key, "_LIKE") && $object->hasKey(substr($key, 0, strpos($key, "_LIKE")))) {
                $query .= " AND " . KeyFormatter::convertKeyToColumn(substr($key, 0, strpos($key, "_LIKE"))) . " LIKE '%" . $val . "%'";
            } elseif (str_contains($key, "_BETWEEN") && $object->hasKey(substr($key, 0, strpos($key, "_BETWEEN")))) {
                $query .= " AND " . KeyFormatter::convertKeyToColumn(substr($key, 0, strpos($key, "_BETWEEN"))) . " BETWEEN '" . $val['from'] . "' AND '" . $val['to'] . '\'';
            } elseif (str_contains($key, "_<=") && $object->hasKey(substr($key, 0, strpos($key, "_<=")))) {
                $query .= " AND " . KeyFormatter::convertKeyToColumn(substr($key, 0, strpos($key, "_<="))) . " <= '" . $val . "'";
            } elseif (str_contains($key, "_>=") && $object->hasKey(substr($key, 0, strpos($key, "_>=")))) {
                $query .= " AND " . KeyFormatter::convertKeyToColumn(substr($key, 0, strpos($key, "_>="))) . " >= '" . $val . "'";
            } elseif (str_contains($key, "_NULL") && $object->hasKey(substr($key, 0, strpos($key, "_NULL")))) {
                $query .= " AND " . KeyFormatter::convertKeyToColumn(substr($key, 0, strpos($key, "_NULL"))) . " IS NULL";
            } elseif (str_contains($key, "_NOTNULL") && $object->hasKey(substr($key, 0, strpos($key, "_NOTNULL")))) {
                $query .= " AND " . KeyFormatter::convertKeyToColumn(substr($key, 0, strpos($key, "_NOTNULL"))) . " IS NOT NULL";
            } elseif ($key == "filters" && is_array($val)) {
                foreach ($val as $k => $v) {
                    $query .= " AND " . KeyFormatter::convertKeyToColumn($k) . " LIKE '%" . $v . "%'";
                }
            } elseif ($object->hasKey($key)) {
                $query .= " AND " . KeyFormatter::convertKeyToColumn($key) . "='" . $val . "'";
            }
        }

        return $query;
    }

    private function matchCurrencyFields(array $constraints, $reflector): array
    {
        foreach ($constraints as $key => $val) {
            $type = $reflector->getTypeByKey($key);
            $result[$key] = $type == "Currency" ? $val * 100 : $val;
        }

        return $result ?? [];
    }

    public function getInsertIntoQuery(object $object, string $parent_class = NULL, int $parent_id = NULL): string
    {
        $table_name = KeyFormatter::getTableNameByObject($object);
        $columns = "";
        $values = "";
        $reflection = new ReflectionClass($object);
        foreach ($reflection->getProperties() as $property) {
            $var = $property->getName();
            $getValue = 'get' . KeyFormatter::processSnakeToPascal($var);
            if (!method_exists(get_class($object), $getValue))
                continue;
            $value = $object->$getValue();
            if (is_iterable($value))
                continue;
            if ($value === NULL)
                continue;
            $columns .= KeyFormatter::convertKeyToColumn($var) . ', ';

            if (gettype($value) == "string" || $property->getType2() == "Date" || $property->getType2() == "DateTime")
                $value = "'" . $value . "'";
            elseif (gettype($value) == "boolean")
                $value = (int)$value;

            $values .= $value . ', ';
        }
        if ($parent_class && !str_contains($columns, KeyFormatter::convertKeyToReferencedColumn($parent_class))) {
            $columns .= KeyFormatter::convertKeyToReferencedColumn($parent_class);
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
        $table_name = KeyFormatter::getTableNameByObject($object);
        $changes = "";
        $reflection = new ReflectionClass($object);
        foreach ($reflection->getProperties() as $property) {
            $var = $property->getName();
            if ($var == 'id')
                continue;
            $getValue = 'get' . KeyFormatter::processSnakeToPascal($var);
            $value = $object->$getValue();
            if (is_iterable($value))
                continue;
            if ($value === NULL)
                continue;
            if (gettype($value) == "string" || $property->getType2() == "Date" || $property->getType2() == "DateTime")
                $value = "'" . $value . "'";
            elseif (gettype($value) == "int" || $property->getType2() == "Currency")
                $value = "'" . $value . "'";
            elseif (gettype($value) == "boolean")
                $value = (int)$value;

            $changes .= KeyFormatter::convertKeyToColumn($var) . ' = ' . $value . ', ';
        }
        $changes = rtrim($changes, ', ');
        $query = 'UPDATE ' . $table_name .
            ' SET ' . $changes .
            ' WHERE ' . KeyFormatter::convertKeyToColumn($where_column) . " = " . $where_val;
        // TODO - Deleted 0 - verify
        if ($parent_where_val && $parent_key)
            $query .= ' AND ' . KeyFormatter::convertKeyToColumn($parent_key) . " = " . $parent_where_val;

        return $query;
    }

    public function getCountQuery(object $object, array $constraints = null): string
    {
        $table_name = NamespaceValidator::isViewEntity($object) ? KeyFormatter::getViewNameByClass($object->getClassName()) : KeyFormatter::getTableNameByObject($object);
        $query = 'SELECT COUNT(*) AS `count` FROM ' . $table_name . ' WHERE `fld_Deleted` = 0';
        if ($constraints)
            $query .= $this->processConstraints($object, $constraints);

        return $query;
    }

    public function getActiveCountQuery(object $object, $where_val = null, string $where_column = 'id'): string
    {
        $table_name = NamespaceValidator::isViewEntity($object) ? KeyFormatter::getViewNameByClass($object->getClassName()) : KeyFormatter::getTableNameByObject($object);
        $query = 'SELECT COUNT(*) AS `count` FROM ' . $table_name . ' WHERE `fld_Deleted` = 0 AND `fld_Active` = 1';
        if ($where_val)
            $query .= ' AND ' . KeyFormatter::convertKeyToColumn($where_column) . ' = ' . $where_val;

        return $query;
    }

    public function getSoftDeleteQuery(object $object, $where_val): string
    {
        $table_name = KeyFormatter::getTableNameByObject($object);
        return 'UPDATE ' . $table_name . ' SET `fld_Deleted` = 1 WHERE `fld_Id` = ' . $where_val;
    }

    public function getCheckIfTableExistsQuery(string $tablename): string
    {
        $query = 'SHOW TABLES LIKE \'%' . $tablename . '%\'';

        return $query;
    }

    private function getPaginationQueryConstraints(object $object, array $params): ?string
    {
        $query = "";
        if ($object->hasKey($params['sortby']))
            $query .= ' ORDER BY ' . KeyFormatter::convertKeyToColumn($params['sortby']) . " " . $params['order'];
        if ($params['limit'])
            $query .= ' LIMIT ' . ($params['page'] * $params['limit']) . ', ' . $params['limit'];

        return $query;
    }

    public function getSelectIdsQuery(object $object, $where_column, $where_value): string
    {
        return 'SELECT `fld_Id` 
                  FROM ' . KeyFormatter::getTableNameByObject($object) . 
                ' WHERE ' . KeyFormatter::convertKeyToColumn($where_column) . ' = ' . $where_value .
                ' AND `fld_Deleted` = 0 AND `fld_Active` = 1';
    }
}
