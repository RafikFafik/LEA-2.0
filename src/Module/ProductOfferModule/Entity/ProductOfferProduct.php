<?php

declare(strict_types=1);

namespace Lea\Module\ProductOfferModule\Entity;

use Lea\Core\Entity\Entity;
use Lea\Core\Type\Currency;

class ProductOfferProduct extends Entity
{    
    /** @var string */
    private $name;

    /** @var string */
    private $unit;
    
    /** @var Currency */
    private $unit_net_price;

    /** @var int */
    private $vat_rate;

    /** @var Currency */
    private $unit_gross_price;

    /** @var int */
    private $quantity;

    /** @var int */
    private $discount_type;

    /** @var int */
    private $discount_value;

    /** @var Currency */
    private $unit_net_price_discounted;

    /** @var Currency */
    private $sum_net_price;

    /** @var Currency */
    private $sum_gross_price;

    /** @var int */
    private $product_offer_id;

    /** @var int */
    private $product_id;


    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of unit
     */ 
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set the value of unit
     *
     * @return  self
     */ 
    public function setUnit(string $unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get the value of unit_net_price
     */ 
    public function getUnitNetPrice()
    {
        return $this->unit_net_price;
    }

    /**
     * Set the value of unit_net_price
     *
     * @return  self
     */ 
    public function setUnitNetPrice(Currency $unit_net_price)
    {
        $this->unit_net_price = $unit_net_price;

        return $this;
    }

    /**
     * Get the value of vat_rate
     */ 
    public function getVatRate()
    {
        return $this->vat_rate;
    }

    /**
     * Set the value of vat_rate
     *
     * @return  self
     */ 
    public function setVatRate(int $vat_rate)
    {
        $this->vat_rate = $vat_rate;

        return $this;
    }

    /**
     * Get the value of unit_gross_price
     */ 
    public function getUnitGrossPrice()
    {
        return $this->unit_gross_price;
    }

    /**
     * Set the value of unit_gross_price
     *
     * @return  self
     */ 
    public function setUnitGrossPrice(Currency $unit_gross_price)
    {
        $this->unit_gross_price = $unit_gross_price;

        return $this;
    }

    /**
     * Get the value of quantity
     */ 
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @return  self
     */ 
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of discount_type
     */ 
    public function getDiscountType()
    {
        return $this->discount_type;
    }

    /**
     * Set the value of discount_type
     *
     * @return  self
     */ 
    public function setDiscountType(int $discount_type)
    {
        $this->discount_type = $discount_type;

        return $this;
    }

    /**
     * Get the value of discount_value
     */ 
    public function getDiscountValue()
    {
        return $this->discount_value;
    }

    /**
     * Set the value of discount_value
     *
     * @return  self
     */ 
    public function setDiscountValue(int $discount_value)
    {
        $this->discount_value = $discount_value;

        return $this;
    }

    /**
     * Get the value of unit_net_price_discounted
     */ 
    public function getUnitNetPriceDiscounted()
    {
        return $this->unit_net_price_discounted;
    }

    /**
     * Set the value of unit_net_price_discounted
     *
     * @return  self
     */ 
    public function setUnitNetPriceDiscounted(Currency $unit_net_price_discounted)
    {
        $this->unit_net_price_discounted = $unit_net_price_discounted;

        return $this;
    }

    /**
     * Get the value of sum_net_price
     */ 
    public function getSumNetPrice()
    {
        return $this->sum_net_price;
    }

    /**
     * Set the value of sum_net_price
     *
     * @return  self
     */ 
    public function setSumNetPrice(Currency $sum_net_price)
    {
        $this->sum_net_price = $sum_net_price;

        return $this;
    }

    /**
     * Get the value of sum_gross_price
     */ 
    public function getSumGrossPrice()
    {
        return $this->sum_gross_price;
    }

    /**
     * Set the value of sum_gross_price
     *
     * @return  self
     */ 
    public function setSumGrossPrice(Currency $sum_gross_price)
    {
        $this->sum_gross_price = $sum_gross_price;

        return $this;
    }

    /**
     * Get the value of product_offer_id
     */ 
    public function getProductOfferId()
    {
        return $this->product_offer_id;
    }

    /**
     * Set the value of product_offer_id
     *
     * @return  self
     */ 
    public function setProductOfferId(int $product_offer_id)
    {
        $this->product_offer_id = $product_offer_id;

        return $this;
    }

    /**
     * Get the value of product_id
     */ 
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set the value of product_id
     *
     * @return  self
     */ 
    public function setProductId(?int $product_id)
    {
        $this->product_id = $product_id;

        return $this;
    }
  }