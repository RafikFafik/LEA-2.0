<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Repository\Repository;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Request\Request;

final class ContractorRepository extends Repository
{
    public function findContractorList(): iterable
    {
        $address_repository = new AddressRepository;
        $user_repository = new UserRepository;
        $constraints = Request::getCustomParams();
        if (isset($constraints['nested']) && filter_var($constraints['nested'], FILTER_VALIDATE_BOOLEAN))
            $list = $this->findList();
        else
            $list = $this->findFlatList();
        foreach ($list as $obj) {
            try {
                $address = $address_repository->findMainHeadquarterByContractorId($obj->getId());
                $obj->address = $address->getAddress();
                $obj->voivodeship = $address->getVoivodeship();
                $obj->voivodeship = $address->getVoivodeship();
                $obj->city = $address->getCity();
            } catch (ResourceNotExistsException $e) {
                $obj->address = null;
                $obj->voivodeship = null;
                $obj->voivodeship = null;
                $obj->city = null;
            }
            $user = $user_repository->findById($obj->getAdvisor());
            $obj->guardian = $user->getName() . " " . $user->getSurname();
        }

        return $list;
    }
}
