<?php

declare(strict_types=1);

namespace Lea\Module\ProductModule\View;

use Lea\Core\View\View;
use Lea\Core\Type\Currency;


class Product extends View
{
  /**
   * @var string
   */
  private $product_name;

  /**
   * @var string
   */
  private $product_model;

  /**
   * @var string
   */
  private $unit;

  /**
   * @var Currency
   */
  private $price;

  /**
   * @var string
   */
  private $producent;

  /**
   * @var string
   */
  private $product_code;
  

  /**
   * Get the value of product_name
   *
   * @return  string
   */ 
  public function getProductName()
  {
    return $this->product_name;
  }

  /**
   * Set the value of product_name
   *
   * @param  string  $product_name
   *
   * @return  self
   */ 
  public function setProductName(string $product_name)
  {
    $this->product_name = $product_name;

    return $this;
  }

  /**
   * Get the value of product_model
   *
   * @return  string
   */ 
  public function getProductModel()
  {
    return $this->product_model;
  }

  /**
   * Set the value of product_model
   *
   * @param  string  $product_model
   *
   * @return  self
   */ 
  public function setProductModel(string $product_model)
  {
    $this->product_model = $product_model;

    return $this;
  }

  /**
   * Get the value of unit
   *
   * @return  string
   */ 
  public function getUnit()
  {
    return $this->unit;
  }

  /**
   * Set the value of unit
   *
   * @param  string  $unit
   *
   * @return  self
   */ 
  public function setUnit(string $unit)
  {
    $this->unit = $unit;

    return $this;
  }

  /**
   * Get the value of producent
   *
   * @return  string
   */ 
  public function getProducent()
  {
    return $this->producent;
  }

  /**
   * Set the value of producent
   *
   * @param  string  $producent
   *
   * @return  self
   */ 
  public function setProducent(string $producent)
  {
    $this->producent = $producent;

    return $this;
  }

  /**
   * Get the value of product_code
   *
   * @return  string
   */ 
  public function getProductCode()
  {
    return $this->product_code;
  }

  /**
   * Set the value of product_code
   *
   * @param  string  $product_code
   *
   * @return  self
   */ 
  public function setProductCode(string $product_code)
  {
    $this->product_code = $product_code;

    return $this;
  }

  /**
   * Get the value of price
   *
   * @return  Currency
   */ 
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * Set the value of price
   *
   * @param  Currency  $price
   *
   * @return  self
   */ 
  public function setPrice(Currency $price)
  {
    $this->price = $price;

    return $this;
  }
}
