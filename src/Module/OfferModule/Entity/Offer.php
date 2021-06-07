<?php

declare(strict_types=1);

namespace Lea\Module\OfferModule\Entity;

use DateTime;
use Lea\Core\Entity\Entity;

class Offer extends Entity
{
    /**
     * @var string
     */
    protected $offer_number = 0;

    /**
     * @var Date
     */
    protected $issue_date;

    /**
     * @var Date
     */
    protected $expiration_date;

    /**
     * @var string
     */
    protected $contractor;

    /**
     * @var string
     */
    protected $contact_person;

    /**
     * @var int
     */
    protected $price_netto;

    /**
     * @var int
     */
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
