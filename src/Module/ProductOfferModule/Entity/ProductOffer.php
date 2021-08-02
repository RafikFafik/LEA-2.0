<?php

declare(strict_types=1);

namespace Lea\Module\ProductOfferModule\Entity;

use Lea\Core\Type\Date;
use Lea\Core\Entity\Entity;

class ProductOffer extends Entity
{
  public const CREATED = 0;
  public const SENT = 1;
  public const ACCEPTED = 2;
  public const REJECTED = 3;
  public const REALIZED = 4;

  /** @var string */
  private $title;
  
  /** @var string */
  private $number;

  /** @var Date */
  private $issue_date;

  /** @var Date */
  private $expiration_date;

  /** @var int */
  private $contractor_id;

  /** @var int */
  private $contact_person_id;

  /** @var int */
  private $project_id;

  /**
   * @var int
   * Using state bindings from model constants
   */
  private $state;

  /** @var string */
  private $description;

  /** @var string */
  private $currency;

  /** @var int */
  private $calc_mode;

  /**
   * @var iterable<ProductOfferFile>
   */
  private $offer_files;

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
   * @return  int
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
   * @return  int
   */ 
  public function setState(int $state)
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

  /**
   * Get the value of title
   */ 
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Set the value of title
   *
   * @return  self
   */ 
  public function setTitle(string $title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get the value of project_id
   */ 
  public function getProjectId()
  {
    return $this->project_id;
  }

  /**
   * Set the value of project_id
   *
   * @return  self
   */ 
  public function setProjectId(?int $project_id)
  {
    $this->project_id = $project_id;

    return $this;
  }

  /**
   * Get the value of currency
   */ 
  public function getCurrency()
  {
    return $this->currency;
  }

  /**
   * Set the value of currency
   *
   * @return  self
   */ 
  public function setCurrency(string $currency)
  {
    $this->currency = $currency;

    return $this;
  }



  /**
   * Get the value of calc_mode
   *
   * @return  int
   */ 
  public function getCalcMode()
  {
    return $this->calc_mode;
  }

  /**
   * Set the value of calc_mode
   *
   * @param  int  $calc_mode
   *
   * @return  self
   */ 
  public function setCalcMode(int $calc_mode)
  {
    $this->calc_mode = $calc_mode;

    return $this;
  }

  /**
   * Get the value of offer_files
   *
   * @return  iterable<ProductOfferFile>
   */ 
  public function getOfferFiles()
  {
    return $this->offer_files;
  }

  /**
   * Set the value of offer_files
   *
   * @param  iterable<ProductOfferFile>  $offer_files
   *
   * @return  self
   */ 
  public function setOfferFiles($offer_files)
  {
    $this->offer_files = $offer_files;

    return $this;
  }
}
