<?php

declare(strict_types=1);

namespace Lea\Core\Type;

class Currency
{
    /**
     * @var int
     */
    private $value;

    public function __construct($value = null, $from_db = false)
    {
        if (!$value)
            $this->value = 0;
            // throw new InvalidCurrencyValueException($value);
        $this->value = $from_db ? (int)$value : $this->formatDenormalizedCurrency((string)$value);
    }

    public function __get($value = null)
    {
        return floatval($this->value / 100);
    }

    public function __toString()
    {
        return (string)$this->value;
    }

    private function formatDenormalizedCurrency(string $value): float
    {
        if(!(str_contains($value, ".") || str_contains($value, ",") || str_contains($value, " ")))
            return (float)$value * 100;

        $value = str_replace(" ", "", $value);
        $value = str_replace(",", ".", $value);
        $pos = strpos($value, ".");
        if(!$pos)
            return (float)$value * 100;

        if (strlen($value) - $pos == 2)
            $value .= "0";
        elseif(strlen($value) - $pos > 3)
            $value = substr($value, 0, $pos + 3);

        return (float)$value * 100;
    }
}
