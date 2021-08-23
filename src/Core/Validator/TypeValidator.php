<?php

declare(strict_types=1);

namespace Lea\Core\Validator;

use Exception;
use TypeError;
use Lea\Core\Type\Date;
use Lea\Core\Type\Currency;
use Lea\Core\Type\DateTime;
use Lea\Core\Reflection\ReflectionProperty;
use Lea\Core\Exception\InvalidDateFormatException;

class TypeValidator implements ValidatorInterface
{
    public const CLIENT = 0;
    public const DATABASE = 1;

    /**
     * @param int $strategy 
     * The ID of strategy delivered by TypeValidator class
     */
    public static function getTypedValue($val, ReflectionProperty $reflector, int $strategy)
    {
        $type = $reflector->getType2();
        switch (strtoupper($type)) {
            case "STRING":
                return $val;
            case "INT":
            case "INTEGER":
                return (int)$val;
                break;
            case "BOOL":
                return filter_var($val, FILTER_VALIDATE_BOOLEAN);
                break;
            case "DATE":
                if (!is_string($val))
                    throw new TypeError($reflector->getName() . " - expected $type");
                try {
                    $type = new Date($val);
                } catch (Exception $e) {
                    throw new InvalidDateFormatException($reflector->getName());
                }
                return $type;
            case "DATETIME":
                if (!is_string($val))
                    throw new TypeError($reflector->getName() . " - expected $type");
                try {
                    $type = new DateTime($val);
                } catch (Exception $e) {
                    throw new InvalidDateFormatException($reflector->getName());
                }
                return $type;
            case "CURRENCY":
                return new Currency($val, $strategy);
            default:
                throw new TypeError($reflector->getName() . ' - undefined type');
                break;
        }
    }
}
