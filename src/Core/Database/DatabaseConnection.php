<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Exception;
use Lea\Response\Response;
use mysqli;
use mysqli_sql_exception;

abstract class DatabaseConnection
{
    public $uid = 0;

    private static $connection;

    public static function establishDatabaseConnection(): void
    {
        $connection = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
        if (!$connection) {
            echo "Error: Unable to connect to MySQL." . PHP_EOL;
            // echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
            // echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }
        $connection->set_charset("utf8");

        self::$connection = $connection;
    }

    public static function getConnection()
    {
        return self::$connection;
    }

    protected static function executeQuery(string $query, string $tableName = null, string $columns = null, object $object = null) // PHP8: mysqli_result|bool
    {
        try {
            if(self::$connection === null)
                Response::internalServerError("No database connection established");
            $mysqli_result = mysqli_query(self::$connection, $query);
        } catch (mysqli_sql_exception $e) {
            $ddl = DatabaseException::handleSqlException($e, self::$connection, $object, $query);
            if (is_array($ddl)) {
                foreach ($ddl as $single_query) {
                    $mysqli_result = self::executeQuery($single_query, $tableName, $columns, $object);
                }
            } else {
                $mysqli_result = self::executeQuery($ddl, $tableName, $columns, $object);
            }
            $mysqli_result = self::executeQuery($query, $tableName, $columns, $object);
        } catch (Exception $e) {
            die("Other non-sql exception");
        }

        return $mysqli_result;
    }
}
