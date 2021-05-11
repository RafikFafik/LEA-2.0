<?php

namespace Lea\Router;

use Lea\Request\Request;
use Lea\Response\Response;

class Router {

    private $routes = [];

    function __construct() {
        $this->routes = json_decode(file_get_contents(__DIR__.'/../../config/routes.json'), TRUE);
        $request = new Request();
        $url = ltrim($request->url(), '/');
        $route = $this->routes[$url] ?? Response::notFound();
        $Controller = $route['controller'];
        
        $controller = new $Controller($request);
        $controller->init();
    }
}
