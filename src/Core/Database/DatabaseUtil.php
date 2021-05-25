<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Exception;
use mysqli_sql_exception;

abstract class DatabaseUtil
{
    protected function executeQuery(string $query, string $tableName, string $columns) // PHP8: mysqli_result|bool
    {
        try {
            $mysqli_result = mysqli_query($this->connection, $query);
        } catch (mysqli_sql_exception $e) {
            $ddl = DatabaseException::handleSqlException($e, $this->connection, $this->object);
            $this->executeQuery($ddl, $tableName, $columns);
            $this->executeQuery($query, $tableName, $columns);
        } catch (Exception $e) {
            die("Other non-sql exception");
        }

        return $mysqli_result;
    }

    protected static function convertKeyToColumn(string $field)
    {
        $field = str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
        return sprintf('`fld_%s`', $field);
    }

    protected static function getTableNameByObject(object $object): string
    {
        $tokens = explode('\\', get_class($object));
        $table = end($tokens);

        return sprintf('`tbl_%ss`', strtolower($table));
    }

    protected function getTableColumnsByObject(object $object): string
    {
        $res = "";
        foreach (get_class_methods($object) as $key) {
            if (strpos($key, 'get') !== false) {
                $key = str_replace('get', '', $key);
                $fld_Key = $this->convertKeyToColumn($key);
                $res .= $fld_Key . ", ";
            }
        }
        $res = rtrim($res, ', ');

        return $res;
    }

    protected function convertToField(string $tableField)
    {
        $tableField = str_replace('fld_', '', $tableField);
        return $tableField;
    }

    protected function getObjectSetters($object): array
    {
        $setters = [];
        foreach (get_class_methods($object) as $key) {
            if (strpos($key, 'set') !== false) {
                $setters[] = $key;
            }
        }

        return $setters;
    }
}
