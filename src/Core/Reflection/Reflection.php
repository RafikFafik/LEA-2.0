<?php

declare(strict_types=1);

namespace Lea\Core\Reflection;

use Exception;
use ReflectionClass;
use ReflectionProperty;

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
            $property->type = $this->getTypePHP7($property);
            $property->is_object = $property->type == NULL ? FALSE : TRUE;
            $this->properties[] = $property;
        }
        $this->namespace = $this->getNamespaceName();
    }

    public function getProperties($filter = null)
    {
        return $filter ? parent::getProperties($filter) : $this->properties;
    }

    public function isObject(): bool
    {
        return $this->is_object ? TRUE : FALSE;
    }

    public function getClassName(): string
    {
        return $this->namespace . "\\" . $this->type;
    }

    private function getTypePHP7(ReflectionProperty $property)
    {
        $comment = $property->getDocComment();
        if (!$comment)
            throw new Exception("TODO - DocComment exception support", 500);
        if (!(int)strpos($comment, "@var"))
            return null;
        $tokens = explode(" ", $comment);
        $index = array_search("@var", $tokens);

        $var = $tokens[$index + 1];

        return $this->getDataType($var);
    }

    private function getDataType(string $data_type)
    {
        if (!(int)strpos($data_type, "<"))
            return null;
        $a = explode("<", $data_type);
        $datatype = explode(">", $a[1])[0];

        return $datatype;
    }
}
