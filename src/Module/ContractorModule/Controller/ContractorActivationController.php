<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\ResourceAlreadyActiveException;
use Lea\Module\ContractorModule\Repository\ContractorRepository;
use Lea\Response\Response;

class ContractorActivationController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch($this->http_method) {
            case "GET":
                try {
                    $repository = new ContractorRepository();
                    $repository->activate($this->params['id']);
                    Response::noContent();
                } catch(ResourceAlreadyActiveException $e) {
                    Response::badRequest("Cannot activate already actived resource: " . $e->getMessage());
                }
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
