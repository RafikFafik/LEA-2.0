<?php

declare(strict_types=1);

namespace Lea\Module\ProductOfferModule\Entity;

use Lea\Core\File\Entity\File;


class ProductOfferFile extends File
{
    /**
     * @var int
     */
    private $product_offer_id;


    /**
     * Get the value of product_offer_id
     *
     * @return  int
     */ 
    public function getProductOfferId()
    {
        return $this->product_offer_id;
    }

    /**
     * Set the value of product_offer_id
     *
     * @param  int  $product_offer_id
     *
     * @return  self
     */ 
    public function setProductOfferId(int $product_offer_id)
    {
        $this->product_offer_id = $product_offer_id;

        return $this;
    }
}