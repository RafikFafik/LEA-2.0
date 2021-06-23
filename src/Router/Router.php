<?php

namespace Lea\Router;

use Error;
use TypeError;
use ArrayIterator;
use MultipleIterator;
use Lea\Request\Request;
use mysqli_sql_exception;
use Lea\Response\Response;
use Symfony\Component\Yaml\Yaml;
use Lea\Core\Validator\Validator;
use Lea\Core\Exception\InvalidDateFormatException;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Exception\UpdatingNotExistingResource;
use Lea\Core\Exception\UserAlreadyAuthorizedException;

final class Router
{

    private $word_regex = "/{(\w+)}/";
    private $query_string_regex = "/\?(\w+)/";
    private $number_regex = "/{(\d+)}/";

    private const URL = 0;
    private const CONFIG = 1;

    private const SQL_ACCESS_DENIED = 1045;
    private const SQL_UNKNOWN_DATABASE = 1049;
    private const SQL_UNKNOWN_HOST = 2002;

    function __construct()
    {
        $routes = Yaml::parseFile(__DIR__ . '/../../config/routes.yaml');
        $request = new Request();
        // TODO - Baza danych
        $module = $this->getEndpointByUrl($routes, $request->url());
        $Controller = $this->getControllerNamespace($module['module_name'], $module['controller']);
        if(isset($module['body-params']))
            Validator::validateBodyParams($module['body-params'], $request->getPayload());
        try {
            $controller = new $Controller($request, $module['params'], $module['allow'] ?? []);
        } catch (Error $e) {
            $message = $e->getMessage();
            if (str_contains($message, "Call to undefined method"))
                Response::internalServerError("Something went wrong - contact with Administrator");
            else if (str_contains($message, "Call to undefined method"))
                Response::internalServerError("Controller not found - contact with Administrator");
            else
                Response::internalServerError("Something went wrong - contact with Administrator");
        }
        try {
            $controller->init();
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
            $message = $e->getMessage();
            $field = substr($message, strpos($message, "set"));
            $field = substr($field, 0, strpos($field, "given") + 5);
            $field = str_replace("()", "", str_replace('set', '', $field));
            $field = self::pascalToSnake($field);
            Response::badRequest("Invalid typeof: " . $field);
        } catch (InvalidDateFormatException $e) {
            Response::badRequest("Invalid date format of: " . $e->getMessage());
        } catch (UserAlreadyAuthorizedException $e) {
            Response::internalServerError("Re-setting user during one connection");
        } catch (ResourceNotExistsException $e) {
            Response::badRequest("Tried to reach resource that not exists: " . $e->getMessage());
        } finally {
            Response::internalServerError("Fatal Error - Contact with Administrator");
        }
    }

    private function getControllerNamespace($module_name, $class_name)
    {
        $namespace = 'Lea\Module\\' . $module_name . '\Controller\\' . $class_name;

        return $namespace;
    }

    private function getEndpointByUrl(array $routes, string $url): array
    {
        foreach ($routes as $module_name => $module) {
            $prefix = "/" . explode("?", explode("/", $url)[1])[0];
            if ($module['prefix'] == $prefix) {
                $endpoint = $this->matchEndpoint($module, $url);
                $endpoint['module_name'] = $module_name;
                $endpoint['params'] = $this->getUrlParams($url, $endpoint['url'], $endpoint['params'] ?? null);


                return $endpoint;
            }
        }

        Response::notFound();
    }

    private function matchEndpoint(array $module, string $request_url): array
    {
        /* Routing 2.0 */
        if ($index = strpos($request_url, "?"))
            $request_url = substr($request_url, 0, $index);


        /* Routing 2.0 */

        foreach ($module['endpoints'] as $endpoint) {
            if ($this->bothHaveTheSameUri($request_url, $endpoint['url']))
                return $endpoint;
        }

        Response::notFound();
    }

    private function bothHaveTheSameUri(string $request_url, string $config_url): bool
    {
        $iterator = $this->getIteratorByUrlPair($request_url, $config_url);
        if ($iterator === NULL)
            return FALSE;

        foreach ($iterator as $i) {
            if (($i[0] === null || $i[1] === null) || ($i[0] !== $i[1] && (!preg_match($this->word_regex, $i[1]) || !preg_match($this->query_string_regex, $i[1])) && !is_numeric($i[0])))
                return FALSE;
        }

        return TRUE;
    }

    private function getUrlParams(string $request_url, string $config_url, array $required_params = null): array
    {
        $iterator = $this->getIteratorByUrlPair($request_url, $config_url);

        $resource_params = [];
        foreach ($iterator as $i) {
            if ($i[0] != $i[1] && preg_match($this->word_regex, $i[1]))
                $resource_params[str_replace(["{", "}"], "", $i[1])] = (int)$i[0];
        }

        if ($index = strpos($request_url, "?")) {
            $keyvals = explode("&", substr($request_url, $index + 1));
            foreach ($keyvals as $keyval) {
                if (!$index = strpos($keyval, "="))
                    Response::badRequest("Incorrect parameter pair: $keyval");
                $key = substr($keyval, 0, $index);
                $val = substr($keyval, $index + 1);
                $val_casted = (int)$val;
                if(strlen((string)$val_casted) == strlen($val))
                    $query_string_params[$key] = $val_casted;
                else
                    $query_string_params[$key] = $val;
            }
            foreach ($required_params ?? [] as $param) {
                if (!array_key_exists($param, $query_string_params))
                    $not_delivered[] = $param;
            }
        } elseif ($required_params) {
            Response::badRequest('Missed query string params: ' . json_encode($required_params));
        }

        if ($not_delivered ?? false)
            Response::badRequest(['Missed query string params' => $not_delivered]);

        return array_merge($resource_params, $query_string_params ?? []);
    }

    private function getIteratorByUrlPair(string $request_url, string $config_url): ?MultipleIterator
    {
        $request_tokens = explode("/", $request_url);
        $config_tokens = explode("/", $config_url);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($request_tokens), "REQUEST");
        $mi->attachIterator(new ArrayIterator($config_tokens), "CONFIG");

        return $mi;
    }

    protected static function pascalToSnake(string $PascalCase): string
    {
        $cammelCase = lcfirst($PascalCase);
        $snake_case = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $cammelCase));

        return $snake_case;
    }
}
