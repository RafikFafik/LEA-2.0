<?php

declare(strict_types=1);

namespace Lea\Core\Validator;

use DateTime;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Response\Response;
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

    public static function validateBodyParams(array $required, array $given): void
    {
        foreach ($required as $key) {
            if (!array_key_exists($key, $given))
                $non_delivered[] = $key;
        }
        if ($non_delivered ?? false)
            Response::badRequest("Missed body params: " . json_encode($non_delivered));
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

        return (string)$month;
    }

    public static function validateRegisterParams(array $params): void
    {
        
        self::validateEmail($params['email']);
        try {
            $repository = new RoleRepository();
            $repository->findById($params['role_id']);
        } catch (ResourceNotExistsException $e) {
            Response::badRequest("Role with given ID does not exists");
        }
    }

    public static function validateEmail(string $email): void
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            Response::badRequest("Invalid email");
    }

    public static function validatePasswordStrength(string $password): void
    {
        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8)
            Response::badRequest("Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character");
    }

    public static function validateAccountActivationParams($params): void
    {
        /* TODO */
    }
}
