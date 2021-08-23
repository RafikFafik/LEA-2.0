<?php

declare(strict_types=1);

namespace Lea\Core\Validator;

class NamespaceValidator implements ValidatorInterface
{
    public static function isViewEntity(object $object): bool
    {
        $tokens = explode('\\', get_class($object));

        return $tokens[3] == "View";
    }
}