<?php

declare(strict_types=1);

namespace Lea\Module\OfferModule\Entity;

use Lea\Core\Entity\Entity;
use Lea\Core\Type\Date;

class Offer extends Entity
{
    /**
     * @var string
     */
    private $title;

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
     * @var int
     */
    protected $contractor_id;
    /**
     * 
     * @var int
     */
    protected $project_id;

    /**
     * @var int
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

    /**
     * @var int
     */
    protected $price_brutto;

    /**
     * @var iterable<OfferFile>
     */
    protected $offer_files;

    /**
     * @var Date
     */
    private $offer_validity_period;

    /**
     * @var Date
     */
    private $delivery_date;

    /**
     * @var string
     */
    private $warranty;

    /**
     * @var string
     */
    private $assembly;

    /**
     * @var string
     */
    private $transport;


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
    public function setOfferNumber(string $offer_number)
    {
        $this->offer_number = $offer_number;

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
     * Get the value of contractor
     *
     * @return  string
     */
    public function getContractorId()
    {
        return $this->contractor_id;
    }

    /**
     * Set the value of contractor
     *
     * @param  string  $contractor
     *
     * @return  self
     */
    public function setContractorId(int $contractor_id)
    {
        $this->contractor_id = $contractor_id;

        return $this;
    }

    /**
     * Get the value of contractor
     *
     * @return  string
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * Set the value of contractor
     *
     * @param  string  $contractor
     *
     * @return  self
     */
    public function setProjectId(int $project_id)
    {
        $this->project_id = $project_id;

        return $this;
    }

    /**
     * Get the value of contact_person
     *
     * @return  string
     */
    public function getContactPerson()
    {
        return $this->contact_person;
    }

    /**
     * Set the value of contact_person
     *
     * @param  string  $contact_person
     *
     * @return  self
     */
    public function setContactPerson(int $contact_person)
    {
        $this->contact_person = $contact_person;

        return $this;
    }

    /**
     * Get the value of price_netto
     *
     * @return  int
     */
    public function getPriceNetto()
    {
        return $this->price_netto;
    }

    /**
     * Set the value of price_netto
     *
     * @param  int  $price_netto
     *
     * @return  self
     */
    public function setPriceNetto(int $price_netto)
    {
        $this->price_netto = $price_netto;

        return $this;
    }

    /**
     * Get the value of price_vat
     *
     * @return  int
     */
    public function getPriceVat()
    {
        return $this->price_vat;
    }

    /**
     * Set the value of price_vat
     *
     * @param  int  $price_vat
     *
     * @return  self
     */
    public function setPriceVat(int $price_vat)
    {
        $this->price_vat = $price_vat;

        return $this;
    }

    /**
     * Get the value of price_brutto
     *
     * @return  int
     */
    public function getPriceBrutto()
    {
        return $this->price_brutto;
    }

    /**
     * Set the value of price_brutto
     *
     * @param  int  $price_brutto
     *
     * @return  self
     */
    public function setPriceBrutto(int $price_brutto)
    {
        $this->price_brutto = $price_brutto;

        return $this;
    }

    /**
     * Get the value of offer_file
     *
     * @return  iterable<OfferFile>
     */
    public function getOfferFiles()
    {
        return $this->offer_files;
    }

    /**
     * Set the value of offer_file
     *
     * @param  string  $offer_file
     *
     * @return  self
     */
    public function setOfferFiles($offer_files)
    {
        $this->offer_files = $offer_files;

        return $this;
    }

    /**
     * Get the value of title
     *
     * @return  string
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param  string  $title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of offer_validity_period
     *
     * @return  Date
     */ 
    public function getOfferValidityPeriod()
    {
        return $this->offer_validity_period;
    }

    /**
     * Set the value of offer_validity_period
     *
     * @param  Date  $offer_validity_period
     *
     * @return  self
     */ 
    public function setOfferValidityPeriod(Date $offer_validity_period)
    {
        $this->offer_validity_period = $offer_validity_period;

        return $this;
    }

    /**
     * Get the value of delivery_date
     *
     * @return  Date
     */ 
    public function getDeliveryDate()
    {
        return $this->delivery_date;
    }

    /**
     * Set the value of delivery_date
     *
     * @param  Date  $delivery_date
     *
     * @return  self
     */ 
    public function setDeliveryDate(Date $delivery_date)
    {
        $this->delivery_date = $delivery_date;

        return $this;
    }

    /**
     * Get the value of warranty
     *
     * @return  string
     */ 
    public function getWarranty()
    {
        return $this->warranty;
    }

    /**
     * Set the value of warranty
     *
     * @param  string  $warranty
     *
     * @return  self
     */ 
    public function setWarranty(string $warranty)
    {
        $this->warranty = $warranty;

        return $this;
    }

    /**
     * Get the value of assembly
     *
     * @return  string
     */ 
    public function getAssembly()
    {
        return $this->assembly;
    }

    /**
     * Set the value of assembly
     *
     * @param  string  $assembly
     *
     * @return  self
     */ 
    public function setAssembly(string $assembly)
    {
        $this->assembly = $assembly;

        return $this;
    }

    /**
     * Get the value of transport
     *
     * @return  string
     */ 
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * Set the value of transport
     *
     * @param  string  $transport
     *
     * @return  self
     */ 
    public function setTransport(string $transport)
    {
        $this->transport = $transport;

        return $this;
    }
}
