<?php

declare(strict_types=1);

namespace Lea\Module\OfferModule\Entity;

use Lea\Core\File\Entity\File;


class OfferFile extends File
{
    /**
     * @var int
     */
    private $offer_id;

    /**
     * Get the value of offer_id
     *
     * @return  int
     */ 
    public function getOfferId()
    {
        return $this->offer_id;
    }

    /**
     * Set the value of offer_id
     *
     * @param  int  $offer_id
     *
     * @return  self
     */ 
    public function setOfferId(int $offer_id)
    {
        $this->offer_id = $offer_id;

        return $this;
    }
}
