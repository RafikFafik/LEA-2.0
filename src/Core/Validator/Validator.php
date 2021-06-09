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
                if(!$is_correct)
                    Response::badRequest("Błędny format daty - użyj yyyy-mm-dd");
            }
        }
    }

    private static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}
