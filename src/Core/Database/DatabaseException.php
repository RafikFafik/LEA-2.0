<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use mysqli_sql_exception;
use ReflectionClass;
use Lea\Core\Database\DatabaseManager;

class DatabaseException
{
    public $uid = 0;
    const SQL_TABLE_NOT_EXISTS = 1146;

    public static function handleSqlException(mysqli_sql_exception $e, $connection, object $object): string
    {
        switch ($e->getCode()) {
            case self::SQL_TABLE_NOT_EXISTS:
                return self::insertTable($object);
            default:
                die("Error not handled yet");
        }
    }

    private static function insertTable(object $object): string
    {
        $tablename = DatabaseManager::getTableNameByObject($object);
        $ddl = 'CREATE TABLE ' . $tablename . ' (';
        $class = new ReflectionClass($object);
        $properties = $class->getProperties();
        $columns = self::parseReflectProperties($properties);
        $ddl .= $columns;

        return $ddl;
    }

    private static function parseReflectProperties(iterable $properties): string
    {
        $columns = "";
        foreach ($properties as $property) {
            $comment = $property->getDocComment();
            $column_name = DatabaseManager::convertKeyToColumn($property->getName());
            $column_properties = self::getVarTypeFromComment($comment);
            if ($column_name == '`fld_Id`') {
                $column_properties = $column_properties . ' NOT NULL AUTO_INCREMENT';
            }
            $columns .= $column_name . " " . $column_properties . ", ";
        }
        $columns = $columns . 'PRIMARY KEY (`fld_Id`)';
        $columns .= ");";

        return $columns;
    }

    private static function describeTable(object $object, $connection): array
    {
        $query = "DESCRIBE ";
        $query .= DatabaseManager::getTableNameByObject($object);
        $result = mysqli_query($connection, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row['Field'];
        }

        return $list;
    }

    private static function getVarTypeFromComment($comment): string
    {
        $lines = explode("\n", $comment);
        $strictType = self::getStrictTypeFromTokenArray($lines);
        switch ($strictType) {
            case "INTEGER":
                return "INT";
            case "INT":
                return "INT";
            case "STRING":
                return "VARCHAR(150)";
            case "JSON":
                return "TEXT";
            case "TEXT":
                return "TEXT";
            case "DATE":
                return "DATE";
            case "DATETIME":
                return "DATETIME";
            case "BOOL":
                return "TINYINT(1)";
            default:
                return "VARCHAR(150)";
        }
    }

    private static function getStrictTypeFromTokenArray(array $lines): ?string
    {
        foreach ($lines as $line) {
            if ((int)strpos($line, "@var")) {
                $tokens = explode(" ", $line);
                $index = array_search("@var", $tokens);

                return strtoupper($tokens[$index + 1]);
            }
        }

        return "VARCHAR(150)";
    }
}
