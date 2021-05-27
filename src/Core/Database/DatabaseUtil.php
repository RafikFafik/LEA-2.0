<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Exception;
use ArrayIterator;
use MultipleIterator;
use mysqli_sql_exception;

abstract class DatabaseUtil
{
    protected function executeQuery(string $query, string $tableName, string $columns, object $object) // PHP8: mysqli_result|bool
    {
        try {
            $mysqli_result = mysqli_query($this->connection, $query);
        } catch (mysqli_sql_exception $e) {
            $ddl = DatabaseException::handleSqlException($e, $this->connection, $object, $query);
            $this->executeQuery($ddl, $tableName, $columns, $object);
            $this->executeQuery($query, $tableName, $columns, $object);
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

        if (substr($table, -1) == 's')
            $result = sprintf('`tbl_%ses`', strtolower($table));
        else
            $result = sprintf('`tbl_%ss`', strtolower($table));

        return $result;
    }

    protected static function getTableColumnsByObject(object $object): string
    {
        $res = "";
        foreach (get_class_methods($object) as $method) {
            if ($object->hasPropertyCorrespondingToMethod($method)) {
                $key = str_replace('get', '', $method);
                $fld_Key = self::convertKeyToColumn($key);
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

    protected function getMultipleIterator(array $row, array $setters, array $reflections): ?MultipleIterator {

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($row), "ROW");
        $mi->attachIterator(new ArrayIterator($setters), "SETTERS");
        $mi->attachIterator(new ArrayIterator($reflections), "PROPERTIES");

        return $mi;
    }

    protected static function processSnakeToPascal(string $text): string
    {
        $result = str_replace('_', '', ucwords($text, '_'));

        return $result;
    }
}
