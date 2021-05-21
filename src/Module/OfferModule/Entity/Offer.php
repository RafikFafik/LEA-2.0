<?php

namespace Lea\Module\OfferModule\Entity;

use Lea\Core\Entity\Entity;

class Offer extends Entity
{
    protected $offer_number = 0;
    protected $issue_date;
    protected $expiration_date;
    protected $contractor;
    protected $contact_person;
    protected $price_netto;
    protected $price_vat;
    protected $price_brutto;
    protected $offer_file;
    protected $number = 0;

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    /**
     * Get the value of price_brutto
     */
    public function getPriceBrutto()
    {
        return $this->price_brutto;
    }

    /**
     * Set the value of price_brutto
     *
     * @return  self
     */
    public function setPriceBrutto($price_brutto)
    {
        $this->price_brutto = $price_brutto;

        return $this;
    }

    /**
     * Get the value of offer_number
     */
    public function getOfferNumber()
    {
        return $this->offer_number;
    }

    /**
     * Set the value of offer_number
     *
     * @return  self
     */
    public function setOfferNumber($offer_number)
    {
        $this->offer_number = $offer_number;

        return $this;
    }
}
