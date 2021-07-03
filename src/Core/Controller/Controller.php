<?php

declare(strict_types=1);

namespace Lea\Core\Controller;

use Lea\Request\Request;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Database\DatabaseConnection;
use Lea\Core\Validator\Validator;
use Lea\Module\Security\Service\TokenVerificationService;

abstract class Controller implements ControllerInterface
{
    protected $request;

    function __construct(Request $request, array $params = NULL, array $allow = NULL)
    {
        $this->request = $request;
        $this->params = $params;
        $this->allow = $allow;

        if ($params)
            Validator::validateParams($params);

        DatabaseConnection::establishDatabaseConnection();

        if (in_array("all", $allow))
            return;
            
        $auth = new TokenVerificationService();
        $this->user = $auth->authorize();
    }
}
