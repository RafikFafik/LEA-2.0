<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\View;

use Lea\Core\View\View;

class Contractor extends View
{
    /**
     * @from Contractor
     * @property $fullname
     * @var string
     */
    private $fullname;
    
    /**
     * @from Contractor
     * @property $shortname
     * @var string
     */
    private $shortname;
    
    /**
     * @from Contractor
     * @property $shortname
     * @var string
     */
    private $city;
    
    /**
     * @from Contractor
     * @property $shortname
     * @var string
     */
    private $voivodeship;
    
    /**
     * @from Contractor
     * @property $shortname
     * @var string
     */
    private $address;

    /**
     * @from Contractor
     * @property $shortname
     * @var string
     */
    private $email;

    /**
     * @from Contractor
     * @property $shortname
     * @var string
     */
    private $nip;
    
    /**
     * @from Contractor
     * @property $shortname
     * @var string
     */
    private $guardian;

    /**
     * Get the value of fullname
     *
     * @return  string
     */ 
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set the value of fullname
     *
     * @param  string  $fullname
     *
     * @return  self
     */ 
    public function setFullname(string $fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get the value of city
     *
     * @return  string
     */ 
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @param  string  $city
     *
     * @return  self
     */ 
    public function setCity(string $city)
    {
        $this->city = $city;

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
     * Get the value of address
     *
     * @return  string
     */ 
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @param  string  $address
     *
     * @return  self
     */ 
    public function setAddress(string $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of email
     *
     * @return  string
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param  string  $email
     *
     * @return  self
     */ 
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of nip
     *
     * @return  string
     */ 
    public function getNip()
    {
        return $this->nip;
    }

    /**
     * Set the value of nip
     *
     * @param  string  $nip
     *
     * @return  self
     */ 
    public function setNip(string $nip)
    {
        $this->nip = $nip;

        return $this;
    }

    /**
     * Get the value of guardian
     */ 
    public function getGuardian()
    {
        return $this->guardian;
    }

    /**
     * Set the value of guardian
     *
     * @return  self
     */ 
    public function setGuardian($guardian)
    {
        $this->guardian = $guardian;

        return $this;
    }

    /**
     * Get the value of shortname
     *
     * @return  string
     */ 
    public function getShortname()
    {
        return $this->shortname;
    }

    /**
     * Set the value of shortname
     *
     * @param  string  $shortname
     *
     * @return  self
     */ 
    public function setShortname(string $shortname)
    {
        $this->shortname = $shortname;

        return $this;
    }
}
