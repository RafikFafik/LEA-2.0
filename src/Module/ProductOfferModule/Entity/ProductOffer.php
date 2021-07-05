<?php

declare(strict_types=1);

namespace Lea\Module\ProductOfferModule\Entity;

use Lea\Core\Type\Date;
use Lea\Core\Entity\Entity;

class ProductOffer extends Entity
{
  /**
   * @var string
   */
  private $number;

  /**
   * @var Date
   */
  private $issue_date;

  /**
   * @var Date
   */
  private $expiration_date;

  /**
   * @var int
   */
  private $contractor_id;

  /**
   * @var int
   */
  private $contact_person_id;

  /**
   * @var string
   */
  private $state;

  /**
   * @var string
   */
  private $description;

  /**
   * @var iterable<ProductOfferProduct>
   */
  private $products;

  /**
   * Get the value of number
   *
   * @return  string
   */ 
  public function getNumber()
  {
    return $this->number;
  }

  /**
   * Set the value of number
   *
   * @param  string  $number
   *
   * @return  self
   */ 
  public function setNumber(string $number)
  {
    $this->number = $number;

    return $this;
  }

  /**
   * Get the value of expiration_date
   *
   * @return  Date
   */ 
  public function getExpirationDate()
  {
    return $this->expiration_date;
  }

  /**
   * Set the value of expiration_date
   *
   * @param  Date  $expiration_date
   *
   * @return  self
   */ 
  public function setExpirationDate(Date $expiration_date)
  {
    $this->expiration_date = $expiration_date;

    return $this;
  }

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

  /**
   * Get the value of contact_person_id
   *
   * @return  int
   */ 
  public function getContactPersonId()
  {
    return $this->contact_person_id;
  }

  /**
   * Set the value of contact_person_id
   *
   * @param  int  $contact_person_id
   *
   * @return  self
   */ 
  public function setContactPersonId(int $contact_person_id)
  {
    $this->contact_person_id = $contact_person_id;

    return $this;
  }

  /**
   * Get the value of state
   *
   * @return  string
   */ 
  public function getState()
  {
    return $this->state;
  }

  /**
   * Set the value of state
   *
   * @param  string  $state
   *
   * @return  self
   */ 
  public function setState(string $state)
  {
    $this->state = $state;

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
   * Get the value of products
   *
   * @return  iterable<ProductOfferProduct>
   */ 
  public function getProducts()
  {
    return $this->products;
  }

  /**
   * Set the value of products
   *
   * @param  iterable<ProductOfferProduct>  $products
   *
   * @return  self
   */ 
  public function setProducts($products)
  {
    $this->products = $products;

    return $this;
  }

  /**
   * Get the value of issue_date
   *
   * @return  Date
   */ 
  public function getIssueDate()
  {
    return $this->issue_date;
  }

  /**
   * Set the value of issue_date
   *
   * @param  Date  $issue_date
   *
   * @return  self
   */ 
  public function setIssueDate(Date $issue_date)
  {
    $this->issue_date = $issue_date;

    return $this;
  }
}