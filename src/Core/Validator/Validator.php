<?php

namespace Lea\Core\Validator;

use DateTime;
use Lea\Response\Response;

class Validator implements ValidatorInterface
{
    public static function validateParams(array $params)
    {
        foreach ($params as $key => $val) {
            if ($key == 'date') {
                $is_correct = self::validateDate($val);
                if (!$is_correct)
                    Response::badRequest("Invalid date format - use yyyy-mm-dd");
            }
            if ($key == 'month') {
                $is_correct = self::validateMonth($val);
                if (!$is_correct)
                    Response::badRequest("Invalid month value - expected integrer between 1-12");
            }
        }
    }

    private static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    private static function validateMonth($month): bool
    {
        if ($month < 0 || $month > 12)
            return false;

        return true;
    }

    public static function parseMonth(string $month): string
    {
        $month = (int)$month;
        if($month < 10)
            $month = "0" . $month;

        return $month;
    }
}
