<?php

declare(strict_types=1);

namespace Lea\Module\OfferModule\Service;

use Lea\Core\Service\Service;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\View\ViewGenerator;
use Lea\Module\ContractorModule\Entity\ContractorEmployee;
use Lea\Module\ContractorModule\Repository\ContractorRepository;
use Lea\Module\ContractorModule\Repository\ContractorEmployeeRepository;

final class OfferService extends Service
{
    public function getView(): iterable
    {
        $contractor_repository = new ContractorRepository();
        $employee_repository = new ContractorEmployeeRepository();
        $list = $this->repository->findFlatList();
        foreach($list as $obj) {
            $contractor = $contractor_repository->findById($obj->getContractorId());
            $employee = $employee_repository->findById($obj->getContactPerson());

            $obj->fullname = $contractor->getFullname();
            $obj->contact_person_name = $employee->getName() . " " . $employee->getSurname();

        }

        $view = new ViewGenerator($this->repository);
        $array = Normalizer::denormalizeList($list);

        return $view->formatPagination($array);
    }

    public function getOfferById(int $id): array
    {
        $object = $this->repository->findById($id);
        $result = Normalizer::denormalize($object);
        $contractor_employee = (new ContractorEmployeeRepository())->findById($object->getContactPerson());
        $result['contractor_employee'] = $contractor_employee->get();
    
        return $result;
    }
}
