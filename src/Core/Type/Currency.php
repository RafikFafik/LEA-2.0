<?php

declare(strict_types=1);

namespace Lea\Core\Type;


class Currency
{
    const DOT_POS = -3;
    /**
     * @var int
     */
    private $value;

    public function __construct($value = null)
    {
        if (!$value)
            return;
        if(is_string($value))
            $value = str_replace(" ", "", $value);
        $this->value = is_float($value) || is_int($value) || (is_string($value) && strlen($value) > 3 && $value[self::DOT_POS] == ".") ? $value * 100 : (int)$value;
    }

    public function __get($value = null)
    {
        return floatval($this->value / 100);
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}
