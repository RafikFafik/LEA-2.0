<?php

declare(strict_types=1);

namespace Lea\Module\ProductOfferModule\Entity;

use Lea\Core\Entity\Entity;

class ProductOfferProduct extends Entity
{
    /**
     * @var int
     */
    private $product_id;
    
    /**
     * @var int
     */
    private $quantity;

    /**
     * @var int
     */
    private $net_price;

    /**
     * @var int
     */
    private $vat_rate;

    /**
     * Get the value of quantity
     *
     * @return  int
     */ 
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @param  int  $quantity
     *
     * @return  self
     */ 
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of net_price
     *
     * @return  float
     */ 
    public function getNetPrice()
    {
        return $this->net_price;
    }

    /**
     * Set the value of net_price
     *
     * @param  float  $net_price
     *
     * @return  self
     */ 
    public function setNetPrice(int $net_price)
    {
        $this->net_price = $net_price;

        return $this;
    }

    /**
     * Get the value of vat_rate
     *
     * @return  int
     */ 
    public function getVatRate()
    {
        return $this->vat_rate;
    }

    /**
     * Set the value of vat_rate
     *
     * @param  int  $vat_rate
     *
     * @return  self
     */ 
    public function setVatRate(int $vat_rate)
    {
        $this->vat_rate = $vat_rate;

        return $this;
    }

    /**
     * Get the value of product_id
     *
     * @return  int
     */ 
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set the value of product_id
     *
     * @param  int  $product_id
     *
     * @return  self
     */ 
    public function setProductId(int $product_id)
    {
        $this->product_id = $product_id;

        return $this;
    }
}