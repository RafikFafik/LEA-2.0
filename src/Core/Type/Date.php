<?php

declare(strict_types=1);

namespace Lea\Core\Type;

use DateTime;

class Date extends DateTime
{
    public function __toString()
    {
        return $this->format("Y-m-d");
    }

    private static function __diff($dt1, $dt2 = NULL)
    {
        $a = gettype($dt1) === "string" ? new DateTime($dt1) : $dt1;
        $b = gettype($dt2) === "string" ? new DateTime($dt2) : $dt2 ?? new DateTime();
        return $a->diff($b);
    }

    public function __get($name = false)
    {
        return $this->__toString();
    }

    public function days2($date)
    {
        $to = gettype($date) === "string" ? new \DateTime($date) : $date;
        return (int)$this->__diff($this->date, $to)->format('%R%a');
    }
}
