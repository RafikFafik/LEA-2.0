<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use ArrayIterator;
use MultipleIterator;
use Lea\Core\Type\Date;
use Lea\Core\Reflection\Reflection;

abstract class DatabaseUtil extends DatabaseConnection
{
    protected static function convertKeyToColumn(string $field): string
    {
        $field = str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
        return sprintf('`fld_%s`', $field);
    }

    protected static function convertParentClassToForeignKey(string $class): string
    {
        $key = self::processPascalToSnake($class);

        return $key . '_id';
    }

    protected static function convertKeyToReferencedColumn(string $field): string
    {
        $field = self::convertKeyToColumn($field);

        return rtrim($field, '`') . 'Id`';
    }

    protected static function getTableNameByObject(object $object): string
    {
        $tokens = explode('\\', get_class($object));
        $class = end($tokens);

        return self::getTableNameByClass($class);
    }

    protected static function getTableNameByClass(string $class): string
    {
        $tokens = explode('\\', $class);
        $table = end($tokens);
        $table = self::processPascalToSnake($table);

        if (substr($table, -1) == 's')
            $result = sprintf('`tbl_%ses`', $table);
        else if (substr($table, -1) == 'y') {
            $result = sprintf('`tbl_%sies`', rtrim($table, 'y'));
        }
        else
            $result = sprintf('`tbl_%ss`', $table);

        return $result;
    }

    protected static function getTableColumnsByObject(object $object): string
    {
        /* TODO - Probably contains mistakes, include columns that are objects */
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

    protected static function getTableColumnsByReflector(Reflection $reflection): string
    {
        $res = "";
        foreach ($reflection->getPrimitiveProperties() as $property) {
            $method = 'get' . self::processSnakeToPascal($property->getName());
            if (method_exists($reflection->getName(), $method)) {
                $key = str_replace('get', '', $method);
                $fld_Key = self::convertKeyToColumn($key);
                $res .= $fld_Key . ", ";
            }
        }
        $res = rtrim($res, ', ');

        return $res;
    }

    protected static function convertToKey(string $tableField)
    {
        $tableField = str_replace('fld_', '', $tableField);
        $tableField = self::processPascalToSnake($tableField);

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

    protected static function getMultipleIterator(array $row, array $setters, array $reflections): ?MultipleIterator
    {

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

    protected static function processPascalToSnake(string $PascalCase): string
    {
        $cammelCase = lcfirst($PascalCase);
        $snake_case = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $cammelCase));

        return $snake_case;
    }

    protected static function processListOfGettersToToSnakeCase(iterable $list): iterable
    {
        foreach($list as $element) {
            $result[] = self::processPascalToSnake(str_replace('get', '', $element));
        }

        return $result ?? [];
    }

    protected static function convertArrayToKeys(array $table_fields): array
    {
        foreach ($table_fields as $field) {
            $keys[] = self::convertToKey($field);
        }

        return $keys ?? [];
    }

    protected static function convertKeyWithFilterToKey(string $key_with_filter): string
    {
        return "";
    }

    protected static function castVariable($variable, string $type_to_cast)
    {
        switch (strtoupper($type_to_cast)) {
            case "INT":
                return (int)$variable;
                break;
            case "BOOL":
                return (bool)$variable;
                break;
            case "DATE":
                return new Date($variable);
            default:
                return $variable;
                break;
        }
    }
}
