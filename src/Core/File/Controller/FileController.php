<?php

namespace Lea\Core\File\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\File\Service\FileService;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Security\Service\TokenVerificationService;

class FileController extends Controller implements ControllerInterface
{
    public function __construct(Request $request, array $params = NULL, array $allow = NULL)
    {
        parent::__construct($request, $params, $allow);
        if(isset($params['auth']) && $params['auth'] == 'true') {
            $auth = new TokenVerificationService();
            $this->user = $auth->authorize();
        }
    }
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                $service = new FileService();
                $data = $service->previewFile((string)$this->params['hash']);

                Response::file($data);

            default:
                Response::methodNotAllowed();
        }
    }
}
