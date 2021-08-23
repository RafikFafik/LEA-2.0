<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Entity;

use Lea\Core\File\Entity\File;


class ContractorFile extends File
{
    /**
     * @var int
     */
    private $contractor_id;

    /**
     * Get the value of contractor_id
     *
     * @return  int
     */ 
    public function getContractorId()
    {
        return $this->contractor_id;
    }

    /**
     * Set the value of contractor_id
     *
     * @param  int  $contractor_id
     *
     * @return  self
     */ 
    public function setContractorId(int $contractor_id)
    {
        $this->contractor_id = $contractor_id;

        return $this;
    }
}
