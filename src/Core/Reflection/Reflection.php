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

    public function __construct(object $object)
    {
        parent::__construct($object);
        $protected_properties = $this->getProperties(ReflectionProperty::IS_PROTECTED);
        $private_properties = $this->getProperties(ReflectionProperty::IS_PRIVATE);
        $properties = array_merge($protected_properties, $private_properties);
        foreach ($properties as $property) {
            $property->type = ReflectionPropertyExtended::getTypePHP7($property);
            $property->is_object = $property->type == NULL ? FALSE : TRUE;
            $this->properties[] = $property;
        }
        $this->namespace = $this->getNamespaceName();
    }

    public function getProperties($filter = null): array
    {
        return $filter ? parent::getProperties($filter) : $this->properties;
    }

    public function getPrimitiveProperties(): array {
        foreach($this->properties as $property) {
            if(!$property->is_object)
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
