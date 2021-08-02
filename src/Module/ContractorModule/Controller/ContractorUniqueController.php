<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ContractorModule\Repository\ContractorRepository;
use Lea\Response\Response;

class ContractorUniqueController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->http_method) {
            case "GET":
                $repository = new ContractorRepository();
                if(!isset($this->params['shortname']))
                    Response::badRequest("Missed `shortname` query string param");
                Response::ok(['is_unique' => $repository->shortnameIsUnique((string)$this->params['shortname'])]);
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}
