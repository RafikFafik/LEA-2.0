<?php

declare(strict_types=1);

namespace Lea\Core\Security\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Security\Service\CommercialAdvisorService;
use Lea\Core\Serializer\Normalizer;
use Lea\Response\Response;

final class CommercialAdvisorController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch($this->http_method) {
            case "GET":
               $service = new CommercialAdvisorService();
               $list = $service->getCommercialAdvisors($this->config['advisor-role-id']);
               $result = Normalizer::denormalizeList($list);
               $result = Normalizer::removeSpecificFieldsFromArrayList($result, ['password', 'active', 'deleted', 'token', 'phone']);
               Response::ok($result);
            default:
                Response::methodNotAllowed();
        }
    }
}
