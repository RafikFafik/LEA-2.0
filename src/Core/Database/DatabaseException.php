<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use ReflectionClass;
use mysqli_sql_exception;
use Lea\Core\Database\DatabaseUtil;
use Lea\Core\Reflection\Reflection;
use Lea\Core\Database\DatabaseManager;

class DatabaseException extends DatabaseUtil
{
    public $uid = 0;
    const SQL_TABLE_NOT_EXISTS = 1146;
    const SQL_UNKNOWN_COLUMN = 1054;
    const SQL_SYMTAMX_ERRORM = 1064;
    const SQL_MISSING_DEFAULT_VALUE = 1364;
    const SQL_FOREIGN_KEY_COHESION = 1451;

    public static function handleSqlException(mysqli_sql_exception $e, $connection, object $object, string $query)
    {
        switch ($e->getCode()) {
            case self::SQL_TABLE_NOT_EXISTS:
                return self::getCreateTableQueryRecursive($object);
            case self::SQL_UNKNOWN_COLUMN:
                die("TODO - Alter table: " . $e->getCode() . "\n" . $e->getMessage());
                // return self::alterTable($object);
            case self::SQL_MISSING_DEFAULT_VALUE:
                die("TODO - Default Value failure: " . $e->getCode() . "\n" . $e->getMessage());
            case self::SQL_FOREIGN_KEY_COHESION:
                die("Trying delete or update ROW that has REFERENCES: " . $e->getCode() . "\n" . $e->getMessage());
            case self::SQL_SYMTAMX_ERRORM:
                die("Symtamx error \n $query");
            default:
                die("Error not handled yet: " . $e->getCode() . "\n" . $e->getMessage());
        }
    }

    private static function alterTable(object $object): string
    {
        return die("alter table not handled yet");
    }
    
    private static function getCreateTableQueryRecursive(object $object, string $parent_table = NULL): array {
        $tablename = DatabaseManager::getTableNameByObject($object);
        $ddl = 'CREATE TABLE ' . $tablename . ' (';
        $class = new Reflection($object);
        $primitive = $class->getPrimitiveProperties();
        $columns = self::parseReflectProperties($primitive);
        if($parent_table) {
            $foreign_column = self::getParentIdReferencedKeyColumn($parent_table);
            $columns .= ', ' . $foreign_column . ' INT NOT NULL';
            $alter_foreing_constraint = self::getForeignKeyConstraint($tablename, DatabaseManager::getTableNameByClass($parent_table), $foreign_column);
        }
        $ddl .= $columns;
        $ddl .= self::getEndBracket();
        $queries[] = $ddl;
        if($parent_table)
            $queries[] = $alter_foreing_constraint;
        $child_objects = $class->getObjectProperties();
        foreach($child_objects as $obj) {
            $parent = $object->getClassName();
            $obj = new $obj->type;
            $children_queries = array_merge($children_queries ?? [], self::getCreateTableQueryRecursive($obj, $parent));
        }
        $queries = array_merge($queries, $children_queries ?? []);

        return $queries;
    }

    private static function parseReflectProperties(iterable $properties): string
    {
        $columns = "";
        foreach ($properties as $property) {
            $comment = $property->getDocComment();
            /* TODO - DefaultValue */
            $column_name = DatabaseManager::convertKeyToColumn($property->getName());
            $column_properties = self::getVarTypeFromComment($comment);
            if ($column_name == '`fld_Id`') {
                $column_properties = $column_properties . ' NOT NULL PRIMARY KEY AUTO_INCREMENT';
            }
            $columns .= $column_name . " " . $column_properties . ", ";
        }
        $columns = rtrim($columns, ", ");
        
        return $columns;
    }

    private static function getEndBracket(): string {
        return ");";
    }
    
    private static function getAlterTableAddPrimaryKey(string $tablename): string 
    {
        $constraint = 'ALTER TABLE '. $tablename . ' ADD CONSTRAINT PK_' . $tablename . ' PRIMARY KEY (`fld_Id`);';

        return $constraint;
    }

    private static function getForeignKeyConstraint(string $tbl_tablename, string $tbl_parent_table_name, string $fld_Parent): string 
    {
        $formatted_parent = rtrim(ltrim($tbl_tablename, '`'), '`');
        $constraint = 'ALTER TABLE ' . $tbl_tablename . ' ADD CONSTRAINT FK_' . $formatted_parent . ' FOREIGN KEY ('. $fld_Parent .  ') REFERENCES ' . $tbl_parent_table_name . ' (`fld_Id`);';

        return $constraint;
    }

    private static function getParentIdReferencedKeyColumn(string $parent): string
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
