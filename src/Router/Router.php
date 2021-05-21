<?php

namespace Lea\Router;

use ArrayIterator;
use Lea\Request\Request;
use Lea\Response\Response;
use MultipleIterator;
use Symfony\Component\Yaml\Yaml;
use ReflectionClass;
final class Router {

    private $word_regex = "/{(\w+)}/";
    private $number_regex = "/{(\d+)}/";

    function __construct() {
        $routes = Yaml::parseFile(__DIR__.'/../../config/routes.yaml');
        $request = new Request();
        $endpoint = $this->getEndpointByUrl($routes, $request->url());
        $Controller = $this->getControllerNamespace($endpoint['module_name'], $endpoint['controller']);
        $controller = new $Controller($request, $endpoint['params']);
        $controller->init();
    }
    
    private function getControllerNamespace($module_name, $class_name) {
        $namespace = 'Lea\Module\\' . $module_name . '\Controller\\' . $class_name;

        return $namespace;
    }

    private function getEndpointByUrl(array $routes, string $url): array {
        foreach($routes as $module_name => $module) {
            $prefix = "/" . explode("/", $url)[1];
            if($module['prefix'] == $prefix) {
                $endpoint = $this->matchEndpoint($module, $url);
                $endpoint['module_name'] = $module_name;
                $endpoint['params'] = $this->getUrlParams($url, $endpoint['url']);

                return $endpoint;
            }
        }

        Response::notFound();
    }
    
    private function matchEndpoint(array $module, string $request_url): array {
        // $url = $this->parseUrl($url);
        foreach($module['endpoints'] as $endpoint) {
            if($this->bothHaveTheSameUri($request_url, $endpoint['url']))
                return $endpoint;
        }

        Response::notFound();
    }

    private function bothHaveTheSameUri(string $request_url, string $config_url): bool {
        $iterator = $this->getIteratorByUrlPair($request_url, $config_url);
        if($iterator === NULL)
            return FALSE;

        foreach($iterator as $i) {
            if(($i[0] === NULL) || ($i[0] !== $i[1] && preg_match($this->word_regex, $i[1]) && !is_numeric($i[0])))
                return FALSE;
        }

        return TRUE;
    }

    private function getUrlParams(string $request_url, string $config_url): array {
        $iterator = $this->getIteratorByUrlPair($request_url, $config_url);
        
        $params = [];
        foreach($iterator as $i) {
            if($i[0] != $i[1] && preg_match($this->word_regex, $i[1]))
                $params[str_replace(["{", "}"], "", $i[1])] = $i[0];
        }

        return $params;
    }

    private function getIteratorByUrlPair(string $request_url, string $config_url): ?MultipleIterator {
        $request_tokens = explode("/", $request_url);
        $config_tokens = explode("/", $config_url);

        if(sizeof($request_tokens) != sizeof($config_tokens))
            return NULL;

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($request_tokens), "REQUEST");
        $mi->attachIterator(new ArrayIterator($config_tokens), "CONFIG");

        return $mi;
    }
}
