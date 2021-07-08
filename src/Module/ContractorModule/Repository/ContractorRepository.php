<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Core\Repository\Repository;
use Lea\Core\Security\Repository\UserRepository;

final class ContractorRepository extends Repository
{
    public function findContractorList(): iterable
    {
        $address_repository = new AddressRepository;
        $user_repository = new UserRepository;
        $list = $this->findFlatList();
        foreach($list as $obj) {
            $address = $address_repository->findMainHeadquarterByContractorId($obj->getId());
            $user = $user_repository->findById($obj->getAdvisor());
            $obj->address = $address->getAddress();
            $obj->voivodeship = $address->getVoivodeship();
            $obj->voivodeship = $address->getVoivodeship();
            $obj->city = $address->getCity();
            $obj->guardian = $user->getName() . " " . $user->getSurname();
        }

        return $list;
    }
}
