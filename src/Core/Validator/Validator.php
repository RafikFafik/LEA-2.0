<?php

namespace Lea\Core\Validator;

use DateTime;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Response\Response;
use Lea\Core\Security\Entity\Role;
use Lea\Core\Security\Repository\RoleRepository;

class Validator implements ValidatorInterface
{
    public static function validateParams(array $params)
    {
        foreach ($params as $key => $val) {
            if ($key == 'date') {
                $is_correct = self::validateDate($val);
                if (!$is_correct)
                    Response::badRequest("Invalid date format - use yyyy-mm-dd");
            } elseif ($key == 'month') {
                $is_correct = self::validateMonth($val);
                if (!$is_correct)
                    Response::badRequest("Invalid month value - expected integrer between 1-12");
            } elseif ($key == 'postcode') {
                $is_correct = self::postcodeIsValid($val);
                if (!$is_correct) {
                    Response::badRequest("Invalid postode format - expected [xx-xxx]");
                }
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

    public static function postcodeIsValid(string $postcode): bool
    {
        if (strlen($postcode) != 6)
            return false;

        return preg_match("/\d{2}-\d{3}/", $postcode) ? true : false;
    }

    public static function parseMonth(int $month): string
    {
        $month = (int)$month;
        if ($month < 10)
            $month = "0" . $month;

        return $month;
    }

    public static function validateRegisterParams(array $params): void
    {
        $required = ['email', 'name', 'surname', 'role_id'];
        foreach ($required as $a) {
            if(!array_key_exists($a, $params))
                $non_delivered[] = $a;
        }
        if($non_delivered ?? false)
            Response::badRequest("Missed body params: " . json_encode($non_delivered));

        try {
            RoleRepository::findById($params['role_id'], new Role);
        } catch (ResourceNotExistsException $e) {
            Response::badRequest("Role with given ID does not exists");
        }

    }

    public static function validatePasswordStrength(string $password): void
    {
        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            echo 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
        } else {
            echo 'Strong password.';
        }
    }
}
