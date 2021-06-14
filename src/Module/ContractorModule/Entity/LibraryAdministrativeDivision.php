<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Entity;

use Lea\Core\Entity\Entity;

class LibraryAdministrativeDivision extends Entity
{
    /**
     * @var string
     */
    private $postcode;

    /**
     * @var string
     */
    private $voivodeship;

    /**
     * @var string
     */
    private $district;

    /**
     * @var string
     */
    private $commune;

    /**
     * @var string
     */
    private $comment;
    

    /**
     * Get the value of postcode
     *
     * @return  string
     */ 
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set the value of postcode
     *
     * @param  string  $postcode
     *
     * @return  self
     */ 
    public function setPostcode(string $postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get the value of voivodeship
     *
     * @return  string
     */ 
    public function getVoivodeship()
    {
        return $this->voivodeship;
    }

    /**
     * Set the value of voivodeship
     *
     * @param  string  $voivodeship
     *
     * @return  self
     */ 
    public function setVoivodeship(string $voivodeship)
    {
        $this->voivodeship = $voivodeship;

        return $this;
    }

    /**
     * Get the value of district
     *
     * @return  string
     */ 
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set the value of district
     *
     * @param  string  $district
     *
     * @return  self
     */ 
    public function setDistrict(string $district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get the value of commune
     *
     * @return  string
     */ 
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * Set the value of commune
     *
     * @param  string  $commune
     *
     * @return  self
     */ 
    public function setCommune(string $commune)
    {
        $this->commune = $commune;

        return $this;
    }

    /**
     * Get the value of comment
     *
     * @return  string
     */ 
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the value of comment
     *
     * @param  string  $comment
     *
     * @return  self
     */ 
    public function setComment(string $comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
