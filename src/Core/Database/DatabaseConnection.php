<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Exception;
use Lea\Core\Exception\DatabaseAccessDeniedException;
use mysqli;
use mysqli_sql_exception;

abstract class DatabaseConnection
{
    public $uid = 0;

    protected static function establishDatabaseConnection(): mysqli
    {
        $connection = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
        if (!$connection) {
            echo "Error: Unable to connect to MySQL." . PHP_EOL;
            // echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
            // echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }
        $connection->set_charset("utf8");

        return $connection;
    }

    protected static function executeQuery($connection, string $query, string $tableName, string $columns, object $object) // PHP8: mysqli_result|bool
    {
        try {
            $mysqli_result = mysqli_query($connection, $query);
        } catch (mysqli_sql_exception $e) {
            $ddl = DatabaseException::handleSqlException($e, $connection, $object, $query);
            if (is_array($ddl)) {
                foreach ($ddl as $single_query) {
                    $mysqli_result = self::executeQuery($connection, $single_query, $tableName, $columns, $object);
                }
            } else {
                $mysqli_result = self::executeQuery($connection, $ddl, $tableName, $columns, $object);
            }
            $mysqli_result = self::executeQuery($connection, $query, $tableName, $columns, $object);
        } catch (Exception $e) {
            die("Other non-sql exception");
        }

        return $mysqli_result;
    }
}
