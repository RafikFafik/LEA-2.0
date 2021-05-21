<?php

namespace Lea\Core\Controller;

use Lea\Request\Request;

interface ControllerInterface {
    function __construct(Request $request, array $params = NULL);
    public function init();
}
