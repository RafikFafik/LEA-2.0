<?php

declare(strict_types=1);

namespace Lea\Router;

use ArrayIterator;
use Lea\Core\QueryString\QueryString;
use MultipleIterator;
use Lea\Request\Request;
use Lea\Response\Response;
use Symfony\Component\Yaml\Yaml;
use Lea\Core\Validator\Validator;

final class Router extends ExceptionDriver
{
    private $word_regex = "/{(\w+)}/";
    private $query_string_regex = "/\?(\w+)/";
    private $module;

    function __construct()
    {
        $routes = Yaml::parseFile(__DIR__ . '/../../config/routes.yaml');
        $this->url = (new Request())->url();
        
        $this->matchModule($routes, $request->url());
        $this->matchEndpoint();
        QueryString::init();
        $Controller = $this->getControllerNamespace($module['module_name'], $module['controller']);

        if (isset($module['params']['filters'])) {
            $filters = $this->getFilterParams($module['params']['filters']);
            QueryString::setFilterParams($filters);
        }
        if (isset($module['body-params']))
            Validator::validateBodyParams($module['body-params'], $request->getPayload());
        $this->instantiateController($Controller, $request, $module['params'], $module['allow'] ?? [], $module['config'] ?? []);
        $this->initializeController();
    }

    private function getControllerNamespace($module_name, $class_name)
    {
        if ($module_name == "Security" || $module_name == "Gus" || $module_name == "File")
            $module = "Core";
        else
            $module = "Module";
        $namespace = 'Lea\\' . $module . '\\' . $module_name . '\Controller\\' . $class_name;

        return $namespace;
    }

    private function matchModule(array $routes, string $url): void
    {
        foreach ($routes as $module_name => $confs) {
            $prefix = "/" . explode("?", explode("/", $url)[1])[0];
            if ($confs['prefix'] == $prefix) {
                $this->module = $confs;
                return;
                $endpoint = $this->matchEndpoint($confs, $url);
                $endpoint['module_name'] = $module_name;
                $endpoint['params'] = $this->getUrlParams($url, $endpoint['url'], $endpoint['params'] ?? null);


            }
        }

        Response::notFound();
    }

    private function matchEndpoint(): array
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
}
