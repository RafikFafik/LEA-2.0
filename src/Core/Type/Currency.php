<?php

declare(strict_types=1);

namespace Lea\Core\Type;


class Currency
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value * 100;
    }

    public function __toString()
    {
        return "xD";
    }
}
