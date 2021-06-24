<?php

namespace Lea\Module\File\Controller;

use Exception;
use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\File\Manager\FileService;
use GusApi\Exception\NotFoundException;
use Lea\Core\Controller\ControllerInterface;

class FileController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                $service = new FileService();
                $data = $service->previewFile((string)$this->params['hash']);

                Response::ok($data);

            default:
                Response::methodNotAllowed();
        }
    }
}
