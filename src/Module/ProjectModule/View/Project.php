<?php

declare(strict_types=1);

namespace Lea\Module\ProjectModule\View;

use Lea\Core\Type\Date;
use Lea\Core\View\View;

class Project extends View
{
  /**
   * @from Project
   * @var string
   */
  private $name;

  /**
   * @from Project
   * @var int
   */
  private $cost;

  /**
   * @from Project
   * @var string
   */
  private $status;

  /**
   * @from Project
   * @var string
   */
  private $description;

  /**
   * @from Contractor
   * @var string
   */
  private $contractor_shortname;

  /**
   * @from ContractorEmployee ( name + surname )
   * @var string
   */
  private $guardian;

  /**
   * @from Project
   * @var Date
   */
  private $expiration_date;

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
   * Get the value of cost
   *
   * @return  int
   */ 
  public function getCost()
  {
    return $this->cost;
  }

  /**
   * Set the value of cost
   *
   * @param  int  $cost
   *
   * @return  self
   */ 
  public function setCost(int $cost)
  {
    $this->cost = $cost;

    return $this;
  }

  /**
   * Get the value of status
   *
   * @return  string
   */ 
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * Set the value of status
   *
   * @param  string  $status
   *
   * @return  self
   */ 
  public function setStatus(string $status)
  {
    $this->status = $status;

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
   * Get the value of contractor_shortname
   *
   * @return  string
   */ 
  public function getContractorShortname()
  {
    return $this->contractor_shortname;
  }

  /**
   * Set the value of contractor_shortname
   *
   * @param  string  $contractor_shortname
   *
   * @return  self
   */ 
  public function setContractorShortname(string $contractor_shortname)
  {
    $this->contractor_shortname = $contractor_shortname;

    return $this;
  }

  /**
   * Get the value of guardian
   */ 
  public function getGuardian()
  {
    return $this->guardian;
  }

  /**
   * Set the value of guardian
   *
   * @return  self
   */ 
  public function setGuardian(string $guardian)
  {
    $this->guardian = $guardian;

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
}
