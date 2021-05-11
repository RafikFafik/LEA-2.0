<?php
namespace Lea\Entity;
use Lea\Entity\EntityRepository;
class Offer extends EntityRepository {
    private $offer_number;
    private $issue_date;
    private $expiration_date;
    private $contractor;
    private $contact_person;
    private $price_netto;
    private $price_vat;
    private $price_brutto;
    private $offer_file;
}
