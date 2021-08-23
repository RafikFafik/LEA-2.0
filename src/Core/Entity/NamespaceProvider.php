<?php

namespace Lea\Core\Entity;

trait NamespaceProvider
{
    public static function getNamespace(): string
    {
        return get_called_class();
    }

    public function getClassName(): string
    {
        $tokens = explode('\\', get_class($this));
        $class = end($tokens);

        return $class;
    }

    public function hasKey(string $key): bool
    {
        return property_exists($this, $key);
    }

}