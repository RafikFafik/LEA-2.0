<?php

declare(strict_types=1);

namespace Lea\Module\ProjectModule\View;

use Lea\Core\Type\Date;
use Lea\Core\View\View;

class Project extends View
{
  /**
   * @var string
   */
  private $name;

  /**
   * @var int
   */
  private $cost;

  /**
   * @var string
   */
  private $status;

  /**
   * @var string
   */
  private $description;

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
}
