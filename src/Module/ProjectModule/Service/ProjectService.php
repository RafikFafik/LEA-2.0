<?php

declare(strict_types=1);

namespace Lea\Module\ProjectModule\Service;

use Lea\Core\Service\Service;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\ProjectModule\Entity\Project;
use Lea\Module\ProjectModule\Repository\ProjectRepository;
use Lea\Module\ContractorModule\Repository\ContractorEmployeeRepository;

class ProjectService extends Service
{
    public function getById(int $id): array
    {
        $repository = new ProjectRepository();
        $object = $repository->findById($id);
        $contractor_employee_repository = new ContractorEmployeeRepository();
        $employees = $contractor_employee_repository->findFlatList(['contractor_id' => $object->getContractorId()]);
        $employees = Normalizer::denormalizeList($employees);
        $result = Normalizer::denormalize($object);
        foreach($result['offers'] as $offer) {
            $offer['employees'] = $employees;
            $updated_offers[] = $offer;
        }
        $result['offers'] = $updated_offers ?? [];

        return $result;
    }
}
