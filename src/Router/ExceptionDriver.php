<?php

declare(strict_types=1);

namespace Lea\Router;

use Error;
use Exception;
use TypeError;
use mysqli_sql_exception;
use Lea\Response\Response;
use Lea\Core\Exception\FileNotExistsException;
use Lea\Core\Exception\FileSaveFailedException;
use Lea\Core\Exception\DocCommentMissedException;
use Lea\Core\Exception\InvalidDateFormatException;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Exception\UpdatingNotExistingResource;
use Lea\Core\Exception\InvalidCurrencyValueException;
use Lea\Core\Exception\UserAlreadyAuthorizedException;

abstract class ExceptionDriver
{
    private const SQL_ACCESS_DENIED = 1045;
    private const SQL_UNKNOWN_DATABASE = 1049;
    private const SQL_UNKNOWN_HOST = 2002;

    protected function instantiateController(string $Controller, $request, $params, $allow, $config): void
    {
        try {
            $this->controller = new $Controller($request, $params, $allow ?? [], $config);
        } catch (Error $e) {
            $message = $e->getMessage();
            if (str_contains($message, "Call to undefined method"))
                Response::internalServerError("Something went wrong - contact with Administrator");
            else if (str_contains($message, "Call to undefined method"))
                Response::internalServerError("Controller not found - contact with Administrator");
            else
                Response::internalServerError("Something went wrong - contact with Administrator");
        }
    }

    protected function initializeController(): void
    {
        try {
            $this->controller->init();
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case self::SQL_ACCESS_DENIED:
                    Response::internalServerError("Could not connect to database - check configuration");
                case self::SQL_UNKNOWN_DATABASE:
                    Response::internalServerError("The database specified in the configuration does not exist");
                case self::SQL_UNKNOWN_HOST:
                    die("Unable to connect to the database server - check the host field in the configuration");
                default:
                    die("SQL exception not handled: " . $e);
            }
        } catch (UpdatingNotExistingResource $e) {
            Response::badRequest("Attempt to edit a non-existent resource: " . nl2br($e->getMessage()));
        } catch (TypeError $e) {
            /* TODO - does not work correctly */
            Response::badRequest("Invalid typeof: " . $e->getMessage());
        } catch (InvalidDateFormatException $e) {
            Response::badRequest("Invalid date format of: " . $e->getMessage());
        } catch (UserAlreadyAuthorizedException $e) {
            Response::internalServerError("Re-setting user during one connection");
        } catch (ResourceNotExistsException $e) {
            Response::badRequest("Tried to reach resource that not exists: " . $e->getMessage());
        } catch (FileNotExistsException $e) {
            Response::badRequest("Expected file with file_key: " . $e->getMessage());
        } catch (FileSaveFailedException $e) {
            Response::internalServerError("Saving file failed: " . $e->getMessage());
        } catch (DocCommentMissedException $e) {
            Response::internalServerError("DocComment not defined for field: " . $e->getMessage());
        } catch (InvalidCurrencyValueException $e) {
            Response::internalServerError("Invalid Currency Value: " . $e->getMessage());
        } catch (Error $e) {
            Response::internalServerError($e->getMessage());
        } catch (Exception $e) {
            Response::internalServerError($e->getMessage());
        } finally {
            Response::internalServerError("Fatal Error - Contact with Administrator");
        }
    }

    protected static function pascalToSnake(string $PascalCase): string
    {
        $cammelCase = lcfirst($PascalCase);
        $snake_case = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $cammelCase));

        return $snake_case;
    }
}
