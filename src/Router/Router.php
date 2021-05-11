<?php

namespace Lea\Router;

use Lea\DatabaseBundle\Magager\DatabaseManager\DatabaseManager;

class Router {

    private $routes = [];

    function __construct() {
        $this->routes = file_get_contents(__DIR__.'/../../config/routes.json');
        $db = new DatabaseManager();
    }
}
