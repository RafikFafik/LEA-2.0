<?php

declare(strict_types=1);

namespace Lea\Core\Reflection;

use ReflectionProperty;

class ReflectionPropertyExtended extends ReflectionProperty
{
    private $is_object;

    public function __construct($class, $property)
    {
        parent::__construct($class, $property);
    }

    protected function setType($type): void
    {
    }

    public function isObject(): bool
    {
        return $this->is_object ? TRUE : FALSE;
    }
}
