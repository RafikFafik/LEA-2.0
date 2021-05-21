<?php

namespace Lea\Module\Homepage\Controller;

use Lea\Core\Controller\ControllerInterface;
use Lea\Request\Request;
use Lea\Response\Response;
use Symfony\Component\Yaml\Yaml;

class HomepageController implements ControllerInterface {

    function __construct(Request $request, $params = NULL) {
        $this->request = $request;
    }

    public function init() {
        switch($this->request->method()) {
            case "GET":
                $response = ['API' => "WORKS"];
                Response::response($response);
            default:
                Response::notFound();
        }
    }
}
