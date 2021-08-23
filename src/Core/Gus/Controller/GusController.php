<?php

namespace Lea\Core\Gus\Controller;

use Exception;
use GusApi\Exception\NotFoundException;
use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Gus\Service\GusService;

class GusController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $service = new GusService();
                    $data = $service->returnData('nip', $this->params['nip']);
    
                    Response::ok($data);
                } catch (NotFoundException $e) {
                    Response::badRequest("Invalid NIP");
                } catch (Exception $e) {
                    Response::badGateway("Gus api not responding");
                }
            default:
                Response::methodNotAllowed();
        }
    }
}
