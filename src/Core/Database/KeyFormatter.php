<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Lea\Core\Type\Date;
use Lea\Core\Type\Currency;
use Lea\Core\Reflection\Reflection;
use Lea\Core\Type\DateTime;

final class KeyFormatter
{
    public static function convertKeyToColumn(string $field): string
    {
        $field = str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
        return sprintf('`fld_%s`', $field);
    }

    public static function convertParentClassToForeignKey(string $class): string
    {
        $key = self::processPascalToSnake($class);

        return $key . '_id';
    }

    public static function convertKeyToReferencedColumn(string $field): string
    {
        $field = self::convertKeyToColumn($field);

        return rtrim($field, '`') . 'Id`';
    }

    public static function getTableNameByObject(object $object): string
    {
        $tokens = explode('\\', get_class($object));
        $class = end($tokens);

        /* TODO */
        // return $tokens[3] == "Entity" ? self::getTableNameByClass($class) : self::getViewNameByClass($class);
        return self::getTableNameByClass($class);
    }

    public static function getTableNameByClass(string $class): string
    {
        $tokens = explode('\\', $class);
        $table = end($tokens);
        $table = self::processPascalToSnake($table);

        if (substr($table, -1) == 's')
            $result = sprintf('`tbl_%ses`', $table);
        elseif (substr($table, -1) == 'y') {
            $result = sprintf('`tbl_%sies`', rtrim($table, 'y'));
        }
        else
            $result = sprintf('`tbl_%ss`', $table);

        return $result;
    }

    public static function getViewNameByClass(string $class): string
    {
        $tokens = explode('\\', $class);
        $table = end($tokens);
        $table = self::processPascalToSnake($table);

        if (substr($table, -1) == 's')
            $result = sprintf('`view_%ses`', $table);
        elseif (substr($table, -1) == 'y') {
            $result = sprintf('`view_%sies`', rtrim($table, 'y'));
        }
        else
            $result = sprintf('`view_%ss`', $table);

        return $result;
    }

    public static function getTableColumnsByObject(object $object): string
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

    public static function getTableColumnsByReflector(Reflection $reflection): string
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

    public static function convertToKey(string $tableField)
    {
        $tableField = str_replace('fld_', '', $tableField);
        $tableField = self::processPascalToSnake($tableField);

        return $tableField;
    }

    public function getObjectSetters($object): array
    {
        $setters = [];
        foreach (get_class_methods($object) as $key) {
            if (strpos($key, 'set') !== false) {
                $setters[] = $key;
            }
        }

        return $setters;
    }

    public static function processSnakeToPascal(string $text): string
    {
        $result = str_replace('_', '', ucwords($text, '_'));

        return $result;
    }

    public static function processPascalToSnake(string $PascalCase): string
    {
        $cammelCase = lcfirst($PascalCase);
        $snake_case = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $cammelCase));

        return $snake_case;
    }

    public static function processListOfGettersToToSnakeCase(iterable $list): iterable
    {
        foreach($list as $element) {
            $result[] = self::processPascalToSnake(str_replace('get', '', $element));
        }

        return $result ?? [];
    }

    public static function convertArrayToKeys(array $table_fields): array
    {
        foreach ($table_fields as $field) {
            $keys[] = self::convertToKey($field);
        }

        return $keys ?? [];
    }

    public static function castVariable($variable, string $type_to_cast)
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
            case "DATETIME":
                return new DateTime($variable);
            case "CURRENCY":
                return new Currency($variable, true);
            default:
                return $variable;
                break;
        }
    }
}
