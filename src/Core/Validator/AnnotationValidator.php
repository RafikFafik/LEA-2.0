<?php

declare(strict_types=1);

namespace Lea\Core\Validator;

class AnnotationValidator implements ValidatorInterface
{
    public static function hasPropertyCorrespondingToMethod(object $object, string $method_name, bool $is_setter = FALSE): bool
    {
        $prefix = substr($method_name, 0, 3);
        $type = $is_setter ? 'set' : 'get';
        if ($prefix != $type)
            return FALSE;
        $VarName = substr($method_name, 3);
        $varName = lcfirst($VarName);
        $var_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $varName));
        /* TODO - get_called_class() -> getEntityClass() */
        return property_exists(get_class($object), $var_name);
    }
}
