<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Entity;

use Lea\Core\Entity\Entity;

class Address extends Entity
{
    /**
     * @var bool
     */
    protected $is_default;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $citycode;

    /**
     * @var string
     */
    protected $voivodeship;

    /**
     * @var string
     */
    protected $country;
}
