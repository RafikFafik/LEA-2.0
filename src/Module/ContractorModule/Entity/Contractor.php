<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Entity;

use Lea\Core\Entity\Entity;
use Lea\Module\ContractorModule\Entity\Address;
use Lea\Module\ContractorModule\Entity\Employee;
use Lea\Module\CalendarModule\Entity\CalendarEvent;

class Contractor extends Entity
{
    /**
     * @var string
     */
    private $shortname;
    /**
     * @var string
     */
    private $fullname;

    /**
     * @var string
     */
    private $nip;

    /**
     * @var string
     */
    private $email;

    /**
     * @var int
     */
    private $advisor;

    /**
     * @var string
     */
    private $register_number;

    /**
     * @var iterable<Address>
     */
    private $addresses;

    /**
     * @var iterable<Employee>
     */
    private $employees;

    /**
     * @var iterable<Lea\Module\CalendarModule\Entity\CalendarEvent>
     */
    private $calendar_events;

    /**
     * @var iterable<Lea\Module\OfferModule\Entity\Offer>
     */
    private $offers;

    /**
     * @var iterable<ContractorFile>
     */
    private $contractor_files;


    public function getShortname()
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname)
    {
        $this->shortname = $shortname;

        return $this;
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getNip()
    {
        return $this->nip;
    }

    public function setNip(string $nip)
    {
        $this->nip = $nip;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    public function getAddresses()
    {
        return $this->addresses;
    }

    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;

        return $this;
    }

    public function getEmployees()
    {
        return $this->employees;
    }

    public function setEmployees($employees)
    {
        $this->employees = $employees;

        return $this;
    }

    /**
     * Get the value of calendar_events
     *
     * @return  iterable<CalendarEvent>
     */ 
    public function getCalendarEvents()
    {
        return $this->calendar_events;
    }

    /**
     * Set the value of calendar_events
     *
     * @param  iterable<CalendarEvent>  $calendar_events
     *
     * @return  self
     */ 
    public function setCalendarEvents($calendar_events)
    {
        $this->calendar_events = $calendar_events;

        return $this;
    }

    /**
     * Get the value of offers
     *
     * @return  iterable<Lea\Module\OfferModule\Entity\Offer>
     */ 
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * Set the value of offers
     *
     * @param  iterable<Lea\Module\OfferModule\Entity\Offer>  $offers
     *
     * @return  self
     */ 
    public function setOffers($offers)
    {
        $this->offers = $offers;

        return $this;
    }

    /**
     * Get the value of contractor_files
     *
     * @return  iterable<ContractorFile>
     */ 
    public function getContractorFiles()
    {
        return $this->contractor_files;
    }

    /**
     * Set the value of contractor_files
     *
     * @param  iterable<ContractorFile>  $contractor_files
     *
     * @return  self
     */ 
    public function setContractorFiles($contractor_files)
    {
        $this->contractor_files = $contractor_files;

        return $this;
    }

    /**
     * Get the value of advisor
     *
     * @return  int
     */ 
    public function getAdvisor()
    {
        return $this->advisor;
    }

    /**
     * Set the value of advisor
     *
     * @param  int  $advisor
     *
     * @return  self
     */ 
    public function setAdvisor(int $advisor)
    {
        $this->advisor = $advisor;

        return $this;
    }

    /**
     * Get the value of register_number
     *
     * @return  string
     */ 
    public function getRegisterNumber()
    {
        return $this->register_number;
    }

    /**
     * Set the value of register_number
     *
     * @param  int  $register_number
     *
     * @return  self
     */ 
    public function setRegisterNumber(string $register_number)
    {
        $this->register_number = $register_number;

        return $this;
    }
}
