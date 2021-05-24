<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Entity;

use Lea\Core\Entity\Entity;

class Address extends Entity
{
    protected $is_default;
    protected $address;
    protected $city;
    protected $citycode;
    protected $voivodeship;
    protected $country;
}
