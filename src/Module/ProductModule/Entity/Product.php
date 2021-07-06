<?php

declare(strict_types=1);

namespace Lea\Module\ProductModule\Entity;

use Lea\Core\Type\Date;
use Lea\Core\Entity\Entity;
use Lea\Core\Type\Currency;

class Product extends Entity
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
  private $unit_of_measure;

  /**
   * @var int
   */
  private $price;

  /**
   * @var Date
   */
  private $playground;

  /**
   * @var int
   */
  private $vat_rate;

  /**
   * @var string
   */
  private $producent;

  /**
   * @var int
   */
  private $product_code;

  /**
   * @var string
   */
  private $product_categories;

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
  private $product_files;

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
   * Get the value of unit_of_measure
   *
   * @return  string
   */ 
  public function getUnitOfMeasure()
  {
    return $this->unit_of_measure;
  }

  /**
   * Set the value of unit_of_measure
   *
   * @param  string  $unit_of_measure
   *
   * @return  self
   */ 
  public function setUnitOfMeasure(string $unit_of_measure)
  {
    $this->unit_of_measure = $unit_of_measure;

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
   * @return  int
   */ 
  public function getProductCode()
  {
    return $this->product_code;
  }

  /**
   * Set the value of product_code
   *
   * @param  int  $product_code
   *
   * @return  self
   */ 
  public function setProductCode(int $product_code)
  {
    $this->product_code = $product_code;

    return $this;
  }

  /**
   * Get the value of product_categories
   *
   * @return  string
   */ 
  public function getProductCategories()
  {
    return $this->product_categories;
  }

  /**
   * Set the value of product_categories
   *
   * @param  string  $product_categories
   *
   * @return  self
   */ 
  public function setProductCategories(string $product_categories)
  {
    $this->product_categories = $product_categories;

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
  public function getProductFiles()
  {
    return $this->product_files;
  }

  /**
   * Set the value of product_files
   *
   * @param  iterable<ProductFile>  $product_files
   *
   * @return  self
   */ 
  public function setProductFiles($product_files)
  {
    $this->product_files = $product_files;

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
  public function setPrice(int $price)
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
   * Get the value of playground
   *
   * @return  Date
   */ 
  public function getPlayground()
  {
    return $this->playground;
  }

  /**
   * Set the value of playground
   *
   * @param  Date  $playground
   *
   * @return  self
   */ 
  public function setPlayground(Date $playground)
  {
    $this->playground = $playground;

    return $this;
  }
}
