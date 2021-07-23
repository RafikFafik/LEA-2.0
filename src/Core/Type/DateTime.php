<?php

declare(strict_types=1);

namespace Lea\Core\Type;

use DateTime as DateTimeBuildin;

class DateTime extends DateTimeBuildin
{
    public function __toString()
    {
        return $this->format("Y-m-d H:i");
    }

    private static function __diff($dt1, $dt2 = NULL)
    {
        $a = gettype($dt1) === "string" ? new DateTimeBuildin($dt1) : $dt1;
        $b = gettype($dt2) === "string" ? new DateTimeBuildin($dt2) : $dt2 ?? new DateTimeBuildin();
        return $a->diff($b);
    }

    public function __get($name = false)
    {
        return $this->__toString();
    }

    public function days2($date)
    {
        $to = gettype($date) === "string" ? new DateTimeBuildin($date) : $date;
        return (int)$this->__diff($this->date, $to)->format('%R%a');
    }
}
