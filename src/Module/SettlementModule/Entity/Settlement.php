<?php

declare(strict_types=1);

namespace Lea\Module\SettlementModule\Entity;

use Lea\Core\Type\Date;
use Lea\Core\Entity\Entity;
use Lea\Core\Type\Currency;

class Settlement extends Entity
{
    /**
     * @var int
     */
    private $document_number;

    /**
     * @var string
     */
    private $contractor;

    /**
     * @var int
     */
    private $contractor_id;

    /**
     * @var Date
     */
    private $issue_date;

    /**
     * @var Currency
     */
    private $unsettled_value;

    /**
     * @var Date
     */
    private $payment_deadline;

    /**
     * @var Currency
     */
    private $net_value;

    /**
     * @var Currency
     */
    private $gross_value;

    /**
     * @var int
     */
    private $vat_rate;

    /**
     * @var int
     */
    private $payment_days_number;

    /**
     * @var string
     */
    private $options;

    

    /**
     * Get the value of document_number
     *
     * @return  int
     */ 
    public function getDocumentNumber()
    {
        return $this->document_number;
    }

    /**
     * Set the value of document_number
     *
     * @param  int  $document_number
     *
     * @return  self
     */ 
    public function setDocumentNumber(int $document_number)
    {
        $this->document_number = $document_number;

        return $this;
    }

    /**
     * Get the value of contractor
     *
     * @return  string
     */ 
    public function getContractor()
    {
        return $this->contractor;
    }

    /**
     * Set the value of contractor
     *
     * @param  string  $contractor
     *
     * @return  self
     */ 
    public function setContractor(string $contractor)
    {
        $this->contractor = $contractor;

        return $this;
    }

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
     * Get the value of unsettled_value
     *
     * @return  Currency
     */ 
    public function getUnsettledValue()
    {
        return $this->unsettled_value;
    }

    /**
     * Set the value of unsettled_value
     *
     * @param  Currency  $unsettled_value
     *
     * @return  self
     */ 
    public function setUnsettledValue(Currency $unsettled_value)
    {
        $this->unsettled_value = $unsettled_value;

        return $this;
    }

    /**
     * Get the value of payment_deadline
     *
     * @return  Date
     */ 
    public function getPaymentDeadline()
    {
        return $this->payment_deadline;
    }

    /**
     * Set the value of payment_deadline
     *
     * @param  Date  $payment_deadline
     *
     * @return  self
     */ 
    public function setPaymentDeadline(Date $payment_deadline)
    {
        $this->payment_deadline = $payment_deadline;

        return $this;
    }

    /**
     * Get the value of net_value
     *
     * @return  Currency
     */ 
    public function getNetValue()
    {
        return $this->net_value;
    }

    /**
     * Set the value of net_value
     *
     * @param  Currency  $net_value
     *
     * @return  self
     */ 
    public function setNetValue(Currency $net_value)
    {
        $this->net_value = $net_value;

        return $this;
    }

    /**
     * Get the value of gross_value
     *
     * @return  Currency
     */ 
    public function getGrossValue()
    {
        return $this->gross_value;
    }

    /**
     * Set the value of gross_value
     *
     * @param  Currency  $gross_value
     *
     * @return  self
     */ 
    public function setGrossValue(Currency $gross_value)
    {
        $this->gross_value = $gross_value;

        return $this;
    }

    /**
     * Get the value of vat_rate
     *
     * @return  int
     */ 
    public function getVatRate()
    {
        return $this->vat_rate;
    }

    /**
     * Set the value of vat_rate
     *
     * @param  int  $vat_rate
     *
     * @return  self
     */ 
    public function setVatRate(int $vat_rate)
    {
        $this->vat_rate = $vat_rate;

        return $this;
    }

    /**
     * Get the value of payment_days_number
     *
     * @return  int
     */ 
    public function getPaymentDaysNumber()
    {
        return $this->payment_days_number;
    }

    /**
     * Set the value of payment_days_number
     *
     * @param  int  $payment_days_number
     *
     * @return  self
     */ 
    public function setPaymentDaysNumber(int $payment_days_number)
    {
        $this->payment_days_number = $payment_days_number;

        return $this;
    }

    /**
     * Get the value of options
     *
     * @return  string
     */ 
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the value of options
     *
     * @param  string  $options
     *
     * @return  self
     */ 
    public function setOptions(string $options)
    {
        $this->options = $options;

        return $this;
    }
}
