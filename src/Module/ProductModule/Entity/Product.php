<?php

declare(strict_types=1);

namespace Lea\Module\ProductModule\Entity;

use Lea\Core\Entity\Entity;
use Lea\Core\Type\Currency;

class Product extends Entity
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
   * @var int
   */
  private $vat_rate;

  /**
   * @var string
   */
  private $producer;

  /**
   * @var string
   */
  private $code;

  /**
   * @var string
   */
  private $categories;

  /**
   * @var string
   */
  private $type;

  /**
   * @var string
   */
  private $description;

  /**
   * @var iterable<ProductFile>
   */
  private $files;

  /**
   * Get the value of product_name
   *
   * @return  string
   */ 
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set the value of product_name
   *
   * @param  string  $product_name
   *
   * @return  self
   */ 
  public function setName(string $name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get the value of product_model
   *
   * @return  string
   */ 
  public function getModel()
  {
    return $this->model;
  }

  /**
   * Set the value of product_model
   *
   * @param  string  $product_model
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
   * Get the value of producent
   *
   * @return  string
   */ 
  public function getProducer()
  {
    return $this->producer;
  }

  /**
   * Set the value of producent
   *
   * @param  string  $producent
   *
   * @return  self
   */ 
  public function setProducer(string $producer)
  {
    $this->producer = $producer;

    return $this;
  }

  /**
   * Get the value of product_code
   *
   * @return  string
   */ 
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Set the value of product_code
   *
   * @param  string  $product_code
   *
   * @return  self
   */ 
  public function setCode(string $code)
  {
    $this->code = $code;

    return $this;
  }

  /**
   * Get the value of product_categories
   *
   * @return  string
   */ 
  public function getCategories()
  {
    return $this->categories;
  }

  /**
   * Set the value of product_categories
   *
   * @param  string  $product_categories
   *
   * @return  self
   */ 
  public function setCategories(string $categories)
  {
    $this->categories = $categories;

    return $this;
  }

  /**
   * Get the value of type
   *
   * @return  string
   */ 
  public function getType()
  {
    return $this->type;
  }

  /**
   * Set the value of type
   *
   * @param  string  $type
   *
   * @return  self
   */ 
  public function setType(string $type)
  {
    $this->type = $type;

    return $this;
  }

  /**
   * Get the value of description
   *
   * @return  string
   */ 
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Set the value of description
   *
   * @param  string  $description
   *
   * @return  self
   */ 
  public function setDescription(string $description)
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Get the value of product_files
   *
   * @return  iterable<ProductFile>
   */ 
  public function getFiles()
  {
    return $this->files;
  }

  /**
   * Set the value of product_files
   *
   * @param  iterable<ProductFile>  $product_files
   *
   * @return  self
   */ 
  public function setFiles($files)
  {
    $this->files = $files;

    return $this;
  }

  /**
   * Get the value of price
   *
   * @return  int
   */ 
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * Set the value of price
   *
   * @param  int  $price
   *
   * @return  self
   */ 
public function setPrice(Currency $price)
  {
    $this->price = $price;

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
