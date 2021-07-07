<?php

declare(strict_types=1);

namespace Lea\Core\Type;


class Currency
{
    /**
     * @var int
     */
    private $value;

    public function __construct($value = null)
    {
        if (!$value)
            return;
        $this->value = is_float($value) || is_int($value) ? $value * 100 : (int)$value;
    }

    public function __get($value = null): float
    {
        return $this->value / 100;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}
