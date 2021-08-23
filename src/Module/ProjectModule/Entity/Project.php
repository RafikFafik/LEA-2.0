<?php

declare(strict_types=1);

namespace Lea\Module\ProjectModule\Entity;

use Lea\Core\Type\Date;
use Lea\Core\Entity\Entity;

class Project extends Entity
{
  /**
   * @var int
   */
  private $contractor_id;

  /**
   * @var int
   */
  private $cost;

  /**
   * @var string
   */
  private $description;

  /**
   * @var Date
   */
  private $issue_date;

  /**
   * @var Date
   */
  private $expiration_date;

  /**
   * @var string
   */
  private $status;

  /**
   * @var string
   */
  private $name;

  /**
   * @var int
   */
  private $worth;

  /**
   * @var iterable<ProjectFile>
   */
  private $project_files;
  
  /**
   * @var iterable<Lea\Module\CalendarModule\Entity\CalendarEvent>
   */
  private $calendar_events;

  /**
   * @var iterable<Lea\Module\OfferModule\Entity\Offer>
   */
  private $offers;

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
   * Get the value of worth
   *
   * @return  int
   */ 
  public function getWorth()
  {
    return $this->worth;
  }

  /**
   * Set the value of worth
   *
   * @param  int  $worth
   *
   * @return  self
   */ 
  public function setWorth(int $worth)
  {
    $this->worth = $worth;

    return $this;
  }

  /**
   * Get the value of project_files
   *
   * @return  iterable<ProjectFile>
   */ 
  public function getProjectFiles()
  {
    return $this->project_files;
  }

  /**
   * Set the value of project_files
   *
   * @param  iterable<ProjectFile>  $project_files
   *
   * @return  self
   */ 
  public function setProjectFiles($project_files)
  {
    $this->project_files = $project_files;

    return $this;
  }

  public function getCalendarEvents()
  {
    return $this->calendar_events;
  }

  /**
   * Set the value of project_files
   *
   * @param  iterable<ProjectFile>  $project_files
   *
   * @return  self
   */ 
  public function setCalendarEvents($calendar_events)
  {
    $this->calendar_events = $calendar_events;

    return $this;
  }

  public function getOffers()
  {
    return $this->offers;
  }

  public function setOffers($offers)
  {
    $this->offers = $offers;

    return $this;
  }
}
