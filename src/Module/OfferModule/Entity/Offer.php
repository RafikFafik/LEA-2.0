<?php
namespace Lea\Module\OfferModule\Entity;

use Lea\Entity\EntityRepository;
class Offer {
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
    // public function getOfferNumber(): int {
    //     return $this->offer_number;
    // }
    public function getNumber(): int {
        return $this->number;
    }

    public function setNumber(string $number): void {
        $this->number = $number;
    }
}