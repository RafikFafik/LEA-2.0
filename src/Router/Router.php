<?php

namespace Lea\Router;

use Lea\Request\Request;
use Lea\Response\Response;
use Symfony\Component\Yaml\Yaml;
class Router {

    private $routes = [];

    function __construct() {
        $this->routes = Yaml::parseFile(__DIR__.'/../../config/routes.yaml');
        $request = new Request();
        $url = $request->url();
        $route = $this->routes[$url] ?? Response::forbidden();
        $Controller = $route['controller'];
        
        $controller = new $Controller($request);
        $controller->init();
    }
}
