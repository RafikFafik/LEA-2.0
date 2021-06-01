<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Entity;

use Lea\Core\Entity\Entity;

class Address extends Entity
{
    /**
     * @var bool
     */
    private $is_default;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $citycode;

    /**
     * @var string
     */
    private $voivodeship;

    /**
     * @var string
     */
    private $country;


    public function getIsDefault()
    {
        return $this->is_default;
    }


    public function setIsDefault($is_default)
    {
        $this->is_default = $is_default;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress(string $address)
    {
        $this->address = $address;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity(string $city)
    {
        $this->city = $city;

        return $this;
    }

    public function getCitycode()
    {
        return $this->citycode;
    }

    public function setCitycode(string $citycode)
    {
        $this->citycode = $citycode;

        return $this;
    }

    public function getVoivodeship()
    {
        return $this->voivodeship;
    }

    public function setVoivodeship(string $voivodeship)
    {
        $this->voivodeship = $voivodeship;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(string $country)
    {
        $this->country = $country;

        return $this;
    }
}
