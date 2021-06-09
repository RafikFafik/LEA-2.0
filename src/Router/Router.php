<?php

namespace Lea\Router;

use ArrayIterator;
use Lea\Core\Exception\UpdatingNotExistingResource;
use Lea\Request\Request;
use Lea\Response\Response;
use MultipleIterator;
use mysqli_sql_exception;
use Symfony\Component\Yaml\Yaml;

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
        $controller = new $Controller($request, $module['params']);
        try {
            $controller->init();
        } catch (mysqli_sql_exception $e) {
            switch ($e->getCode()) {
                case self::SQL_ACCESS_DENIED:
                    Response::internalServerError("Nie można nawiązać połączenia z bazą danych - sprawdź konfigurację");
                case self::SQL_UNKNOWN_DATABASE:
                    Response::internalServerError("Wskazana w konfiguracji baza danych nie istnieje");
                case self::SQL_UNKNOWN_HOST:
                    die("Nie można połączyć się z serwerem bazy danych - sprawdź pole host w konfiguracji");
                default:
                    die("SQL exception not handled: " . $e);
            }
        } catch (UpdatingNotExistingResource $e) {
            Response::badRequest("Próba edycji nieistniejącego zasobu");
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
            $prefix = "/" . explode("/", $url)[1];
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

        $params = [];
        foreach ($iterator as $i) {
            if ($i[0] != $i[1] && preg_match($this->word_regex, $i[1]))
                $params[str_replace(["{", "}"], "", $i[1])] = (int)$i[0];
        }

        if ($index = strpos($request_url, "?")) {
            $tokens = explode("=", substr($request_url, $index + 1));
            if (count($tokens) % 2)
                Response::badRequest("Cos nie teges z query string parametrami. Czy aby napewno dodane są wszystkie wymagane?");
            foreach ($required_params ?? [] as $param) {
                if (!in_array($param, $tokens)) {
                    $not_delivered[] = $param;
                } else {
                    $val = $tokens[array_search($param, $tokens) + 1];
                    $params[$param] = $val;
                }
            }
        } elseif ($required_params) {
            Response::badRequest($required_params);
        }

        if ($not_delivered ?? false)
            Response::badRequest($not_delivered);

        return $params;
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
}
