<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Request\Request;
use Lea\Core\Repository\Repository;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Exception\ResourceAlreadyActiveException;
use Lea\Core\Exception\ResourceAlreadyInactiveException;

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

    public function activate(int $id): void
    {
        $dbstate = $this->findById($id);
        if((bool)$dbstate->getActive() === true)
            throw new ResourceAlreadyActiveException($this->object->getClassName());
        $this->object->setId($id);
        $this->object->setActive(true);
        $this->save($this->object);
    }

    public function deactivate(int $id): void
    {
        $dbstate = $this->findById($id);
        if((bool)$dbstate->getActive() === false)
            throw new ResourceAlreadyInactiveException($this->object->getClassName());
        $this->object->setId($id);
        $this->object->setActive(false);
        $this->save($this->object);
    }
}
