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
}
