<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use ReflectionClass;
use mysqli_sql_exception;
use Lea\Core\Database\DatabaseUtil;
use Lea\Core\Reflection\Reflection;
use Lea\Core\Database\DatabaseManager;
use Lea\Core\Reflection\ReflectionPropertyExtended;
use Lea\Response\Response;
use ReflectionProperty;

class DatabaseException extends DatabaseUtil
{
    public $uid = 0;
    const SQL_TABLE_NOT_EXISTS = 1146;
    const SQL_UNKNOWN_COLUMN = 1054;
    const SQL_SYMTAMX_ERRORM = 1064;
    const SQL_MISSING_DEFAULT_VALUE = 1364;
    const SQL_FOREIGN_KEY_COHESION = 1451;
    const SQL_INCORRECT_DATE_VALUE = 1292;
    const SQL_OUT_OF_RANGE = 1264;
    const SQL_FOREIGN_KEY_CONSTRAINTS_FAIL = 1452;
    const SQL_CREATING_REFERENCE_TO_NON_EXISTING_TABLE = 1005;

    public static function handleSqlException(mysqli_sql_exception $e, $connection, object $object, string $query)
    {
        $message = $e->getMessage();
        switch ($e->getCode()) {
            case self::SQL_TABLE_NOT_EXISTS:
                return self::getCreateTableQueryRecursive($object);
            case self::SQL_UNKNOWN_COLUMN:
                return self::getAlterTableQuery($object, $connection);
            case self::SQL_MISSING_DEFAULT_VALUE:
                Response::internalServerError("TODO - Default Value failure: " . $e->getCode() . "\n" . $e->getMessage());
            case self::SQL_FOREIGN_KEY_CONSTRAINTS_FAIL:
            case self::SQL_FOREIGN_KEY_COHESION:
                $substr = substr($message, (int)strpos($message, "FOREIGN KEY") + 12);
                $field = substr($substr, 0, (int)strpos($substr, "REFERENCES") - 1);
                $field = ltrim($field, '(`');
                $field = rtrim($field, '`)');
                $field = self::convertToKey($field);
                Response::badRequest("Invalid value of field that has reference: " . $field);
            case self::SQL_INCORRECT_DATE_VALUE:
                Response::internalServerError("Incorrect date value: " . $e->getCode() . "\n" . $e->getMessage());
            case self::SQL_SYMTAMX_ERRORM:
                Response::internalServerError("Symtamx error \n $query");
            case self::SQL_OUT_OF_RANGE:
                $field = substr($message, strpos($message, "'") + 1);
                $field = substr($field, 0, strpos($field, "'"));
                Response::badRequest("Passed value of ``" . $field . "`` out of range - Contact with Administrator to increase it");
            case self::SQL_CREATING_REFERENCE_TO_NON_EXISTING_TABLE:
                return ""; /* Workaround / todo */
            default:
                Response::internalServerError("Error not handled yet: " . $e->getCode() . "\n" . $e->getMessage());
        }

        return "";
    }

    private static function getAlterTableQuery(object $object, $connection): array
    {
        $described = self::describeTable($object, $connection);
        $table_keys = self::convertArrayToKeys($described);
        $entity_methods = $object->getGetters();
        $last = false;
        foreach ($entity_methods as $method) {
            $key = self::processPascalToSnake(str_replace('get', '', $method));
            if (in_array($key, $table_keys)) {
                $last = $key;
                continue;
            }
            $reflector = new ReflectionPropertyExtended(get_class($object), $key);
            if ($reflector->isObject())
                continue;
            $query = 'ALTER TABLE ' . self::getTableNameByObject($object) . ' ADD ';
            $query .= self::parseReflectProperty($reflector);
            $query .= $last ?  ' AFTER ' . self::convertKeyToColumn($last) . ';' : ";";
            $queries[] = $query;
        }
        // TODO - polish collate

        return $queries ?? [];
    }

    private static function getCreateTableQueryRecursive(object $object, string $parent_table = null): array
    {
        $tablename = DatabaseManager::getTableNameByObject($object);
        $ddl = 'CREATE TABLE ' . $tablename . ' (';
        $class = new Reflection($object);
        $primitive = $class->getPrimitiveProperties();
        $referenced = $object->getReferencedProperties();
        $referenced = self::processListOfGettersToToSnakeCase($referenced);
        $columns = self::parseReflectProperties($primitive);
        if ($parent_table) {
            $foreign_column = self::getReferencedKeyColumn($parent_table);
            $columns .= ', ' . $foreign_column . ' INT NOT NULL';
            $alter_foreing_constraint = self::getForeignKeyConstraint($tablename, DatabaseManager::getTableNameByClass($parent_table), $foreign_column);
        }
        if ($referenced) {
            foreach ($referenced as $table) {
                $foreign_column = self::convertKeyToColumn($table);
                $alter_references[] = self::getForeignKeyConstraint($tablename, DatabaseManager::getTableNameByClass(str_replace('_id', '', $table)), $foreign_column);
            }
        }
        $ddl .= $columns;
        $ddl .= ') CHARACTER SET utf8 COLLATE utf8_polish_ci';
        $ddl .= self::getEndBracket();
        $queries[] = $ddl;
        if ($parent_table)
            $queries[] = $alter_foreing_constraint;
        if ($alter_references ?? false)
            $queries = array_merge($queries, $alter_references);
        $child_objects = $class->getObjectProperties();
        foreach ($child_objects as $obj) {
            $parent = $object->getClassName();
            $Class = $obj->getType2();
            $obj = new $Class;
            $children_queries = array_merge($children_queries ?? [], self::getCreateTableQueryRecursive($obj, $parent));
        }
        $queries = array_merge($queries, $children_queries ?? []);

        return $queries;
    }

    private static function parseReflectProperties(iterable $properties): string
    {
        $columns = "";
        foreach ($properties as $property) {
            $columns .= self::parseReflectProperty($property) . ", ";
        }
        $columns = rtrim($columns, ", ");

        return $columns;
    }

    private static function parseReflectProperty(ReflectionProperty $property): string
    {
        $comment = $property->getDocComment();
        /* TODO - DefaultValue */
        $column_name = DatabaseManager::convertKeyToColumn($property->getName());
        $column_properties = self::getVarTypeFromComment($comment);
        if ($column_name == '`fld_Id`') {
            $column_properties = $column_properties . ' NOT NULL PRIMARY KEY AUTO_INCREMENT';
        }

        return $column_name . " " . $column_properties;
    }

    private static function getEndBracket(): string
    {
        return ";";
    }

    private static function getAlterTableAddPrimaryKey(string $tablename): string
    {
        $constraint = 'ALTER TABLE ' . $tablename . ' ADD CONSTRAINT PK_' . $tablename . ' PRIMARY KEY (`fld_Id`);';

        return $constraint;
    }

    private static function getForeignKeyConstraint(string $tbl_tablename, string $tbl_parent_table_name, string $fld_Parent): string
    {
        $formatted_parent = rtrim(ltrim($tbl_tablename, '`'), '`');
        $formatted_field = rtrim(ltrim($fld_Parent, '`'), '`');
        $constraint = 'ALTER TABLE ' . $tbl_tablename . ' ADD CONSTRAINT FK_' . $formatted_parent . '_' . $formatted_field . ' FOREIGN KEY (' . $fld_Parent .  ') REFERENCES ' . $tbl_parent_table_name . ' (`fld_Id`);';

        return $constraint;
    }

    private static function getReferencedKeyColumn(string $parent): string
    {
        $column = self::convertKeyToColumn($parent);
        $column = rtrim($column, '`');
        $column .= 'Id`';

        return $column;
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
        if ($comment === FALSE)
            return "VARCHAR(150)";
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
