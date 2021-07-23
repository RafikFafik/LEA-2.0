<?php

declare(strict_types=1);

namespace Lea\Core\Reflection;

use ReflectionClass as ReflectionClassBuiltin;
use Lea\Core\Reflection\ReflectionProperty;

final class ReflectionClass extends ReflectionClassBuiltin
{
    private $namespace;
    private $properties = [];
    private $comment;
    private const LINE_FEED = 10;

    public function __construct($objectOrClass)
    {
        parent::__construct($objectOrClass);
        $this->comment = $this->getDocComment();
        $protected_properties = $this->getProperties(ReflectionProperty::IS_PROTECTED);
        $private_properties = $this->getProperties(ReflectionProperty::IS_PRIVATE);
        /* TODO getProperties(ReflectionProperty::WITH_LOG_DOC); */
        $properties = array_merge($protected_properties, $private_properties);
        $this->properties = $this->genericToExtendedPropertyReflection($properties, $objectOrClass);

        $this->namespace = $this->getNamespaceName();
    }

    public function getProperties($filter = null): array
    {
        return $filter ? parent::getProperties($filter) : $this->properties;
    }

    public function getPrimitiveProperties(): array
    {
        foreach ($this->properties as $property) {
            if (!$property->isObject())
                $res[] = $property;
        }

        return $res ?? [];
    }

    public function getReferencedProperties(): array
    {
        foreach ($this->properties as $property) {
            if (!$property->hasReference())
                $res[] = $property;
        }

        return $res ?? [];
    }

    public function getObjectProperties(): array
    {
        foreach ($this->properties as $property) {
            if ($property->isObject())
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

    public function getTypeByKey(string $key): ?string
    {
        foreach ($this->properties as $property) {
            if (str_contains($key, $property->getName()))
                return $property->getType2();
        }

        return null;
    }

    private function genericToExtendedPropertyReflection(array $properties, $objectOrClass): array
    {
        $class = is_object($objectOrClass) ? $objectOrClass->getNamespace() : $objectOrClass;
        foreach ($properties as $property) {
            $result[] = new ReflectionProperty($class, $property->getName());
        }

        return $result ?? [];
    }

    public function hasSubClassDependency(): bool
    {
        return str_contains($this->comment, "@dependency");
    }

    public function getSubClass(): ?string
    {
        return $this->extractDocCommentValue("@dependency");
    }

    public function getSubKey(): ?string
    {
        return $this->extractDocCommentValue("@property");
    }

    private function extractDocCommentValue(string $param): ?string
    {
        if (!(int)strpos($this->comment, $param))
            return null;
        $tokens = explode(" ", $this->comment);
        $index = array_search($param, $tokens);
        $token = str_replace("$", "", $tokens[$index + 1]);
        if (ord($token[-1]) == self::LINE_FEED)
            $val = rtrim($token, "\n");

        return $val;
    }
}
