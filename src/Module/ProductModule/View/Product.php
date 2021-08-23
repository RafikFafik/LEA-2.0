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
  private $name;

  /**
   * @var string
   */
  private $model;

  /**
   * @var string
   */
  private $unit;

  /**
   * @var Currency
   */
  private $net_price;

  /**
   * @var string
   */
  private $producer;

  /**
   * @var string
   */
  private $code;
  

  /**
   * Get the value of name
   *
   * @return  string
   */ 
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set the value of name
   *
   * @param  string  $name
   *
   * @return  self
   */ 
  public function setName(string $name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get the value of model
   *
   * @return  string
   */ 
  public function getModel()
  {
    return $this->model;
  }

  /**
   * Set the value of model
   *
   * @param  string  $model
   *
   * @return  self
   */ 
  public function setModel(string $model)
  {
    $this->model = $model;

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
   * Get the value of producer
   *
   * @return  string
   */ 
  public function getProducer()
  {
    return $this->producer;
  }

  /**
   * Set the value of producer
   *
   * @param  string  $producer
   *
   * @return  self
   */ 
  public function setProducer(string $producer)
  {
    $this->producer = $producer;

    return $this;
  }

  /**
   * Get the value of code
   *
   * @return  string
   */ 
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Set the value of code
   *
   * @param  string  $code
   *
   * @return  self
   */ 
  public function setCode(string $code)
  {
    $this->code = $code;

    return $this;
  }

  /**
   * Get the value of net_price
   *
   * @return  Currency
   */ 
  public function getNetPrice()
  {
    return $this->net_price;
  }

  /**
   * Set the value of net_price
   *
   * @param  Currency  $net_price
   *
   * @return  self
   */ 
  public function setNetPrice(Currency $net_price)
  {
    $this->net_price = $net_price;

    return $this;
  }
}
