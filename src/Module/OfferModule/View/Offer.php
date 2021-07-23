<?php

declare(strict_types=1);

namespace Lea\Module\OfferModule\View;

use Lea\Core\View\View;

class Offer extends View
{
    /**
     * @from Offer
     * @var string
     */
    private $title;

    /**
     * @from Offer
     * @var Date
     */
    private $issue_date;

    /**
     * @from Offer
     * @var Date
     */
    private $expiration_date;

    /**
     * @from Contractor
     * @var string
     */
    private $fullname;

    /**
     * @from ContractorEmployee
     * @var string
     */
    private $contact_person_name;

    /**
     * @from Offer
     * @var int
     */
    private $price_netto;

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
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of issue_date
     */
    public function getIssueDate()
    {
        return $this->issue_date;
    }

    /**
     * Set the value of issue_date
     *
     * @return  self
     */
    public function setIssueDate($issue_date)
    {
        $this->issue_date = $issue_date;

        return $this;
    }

    /**
     * Get the value of expiration_date
     */
    public function getExpirationDate()
    {
        return $this->expiration_date;
    }

    /**
     * Set the value of expiration_date
     *
     * @return  self
     */
    public function setExpirationDate($expiration_date)
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }

    /**
     * Get the value of fullname
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set the value of fullname
     *
     * @return  self
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get the value of contact_person_name
     */
    public function getContactPersonName()
    {
        return $this->contact_person_name;
    }

    /**
     * Set the value of contact_person_name
     *
     * @return  self
     */
    public function setContactPersonName($contact_person_name)
    {
        $this->contact_person_name = $contact_person_name;

        return $this;
    }

    /**
     * Get the value of price_netto
     */
    public function getPriceNetto()
    {
        return $this->price_netto;
    }

    /**
     * Set the value of price_netto
     *
     * @return  self
     */
    public function setPriceNetto($price_netto)
    {
        $this->price_netto = $price_netto;

        return $this;
    }
}
