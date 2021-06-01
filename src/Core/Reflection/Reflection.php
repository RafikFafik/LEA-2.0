<?php

declare(strict_types=1);

namespace Lea\Core\Reflection;

use Exception;
use ReflectionClass;
use ReflectionProperty;
use Lea\Core\Reflection\ReflectionPropertyExtended;

final class Reflection extends ReflectionClass
{
    private $namespace;
    private $properties = [];

    public function __construct($objectOrClass)
    {
        parent::__construct($objectOrClass);
        $protected_properties = $this->getProperties(ReflectionProperty::IS_PROTECTED);
        $private_properties = $this->getProperties(ReflectionProperty::IS_PRIVATE);
        $properties = array_merge($protected_properties, $private_properties);
        foreach ($properties as $property) {
            $type = ReflectionPropertyExtended::getTypePHP7($property);
            if (ctype_upper($type[0])) {
                $property->is_object = TRUE;
                $property->type = $this->getNamespaceName() . "\\" . $type;
            } else {
                $property->is_object = FALSE;
                $property->type = $type;
            }
            $this->properties[] = $property;
        }
        $this->namespace = $this->getNamespaceName();
    }

    public function getProperties($filter = null): array
    {
        return $filter ? parent::getProperties($filter) : $this->properties;
    }

    public function getPrimitiveProperties(): array
    {
        foreach ($this->properties as $property) {
            if (!$property->is_object)
                $res[] = $property;
        }

        return $res ?? [];
    }

    public function getObjectProperties(): array
    {
        foreach ($this->properties as $property) {
            if ($property->is_object)
                $res[] = $property;
        }

        return $res ?? [];
    }

    public function isObject(): bool
    {
        return $this->is_object ? TRUE : FALSE;
    }

    public function getClassName(): string
    {
        return $this->namespace . "\\" . $this->type;
    }
}
