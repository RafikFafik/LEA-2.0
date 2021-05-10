<?php

namespace Lea\Router;

class Router {

    private $routes = [];

    function __construct() {
        $this->routes = file_get_contents('/var/www/LEA2.0/config/routes.json');

        die(json_encode($this->routes));
    }
}
