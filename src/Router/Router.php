<?php

namespace Lea\Router;

class Router {

    private $routes = [];

    function __construct(){
        $this->routes = yaml_parse_file(__DIR__.'/../src/config/routes.yaml');

        die(json_encode($this->routes));
    }
}
