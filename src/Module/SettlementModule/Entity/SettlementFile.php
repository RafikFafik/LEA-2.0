<?php

declare(strict_types=1);

namespace Lea\Module\SettlementModule\Entity;

use Lea\Core\File\Entity\File;

class SettlementFile extends File
{
    /**
     * @var int
     */
    private $Settlement_id;

    /**
     * Get the value of Settlement_id
     *
     * @return  int
     */ 
    public function getSettlementId()
    {
        return $this->Settlement_id;
    }

    /**
     * Set the value of Settlement_id
     *
     * @param  int  $Settlement_id
     *
     * @return  self
     */ 
    public function setSettlementId(int $Settlement_id)
    {
        $this->Settlement_id = $Settlement_id;

        return $this;
    }
}
