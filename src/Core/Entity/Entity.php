<?php

namespace Lea\Core\Entity;

use Generator;
use Lea\Core\Reflection\Reflection;
use ReflectionClass;
use ReflectionProperty;

abstract class Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var bool
     */
    protected $active = 1;

    /**
     * @var bool
     */
    protected $deleted = 0;

    public function __construct(array $data = NULL)
    {
        if ($data !== NULL)
            $this->set($data);
    }

    public function set(array $data): void
    {
        $class = get_called_class();

        foreach ($data as $key => $val) {
            if (!property_exists($class, $key))
                continue;
            $reflection = new Reflection($class, $key);
            $setValue = 'set' . $this->processSnakeToPascal($key);
            if ($reflection->isObject()) {
                if (is_iterable($val)) {
                    $children = [];
                    foreach ($val as $obj) {
                        $ChildClass = $reflection->getClassName();
                        $children[] = new $ChildClass($obj);
                    }
                    $this->$setValue($children);
                }
            } else {
                $this->$setValue($val);
            }
        }
    }

    public function getGetters(): array
    {
        $getters = [];
        foreach (get_class_methods($this) as $method) {
            if ($this->hasPropertyCorrespondingToMethod($method))
                $getters[] = $method;
        }

        return $getters;
    }

    public function getChildObjects(): iterable
    {
        $class = get_called_class();

        $reflection = new ReflectionClass($class);
        $protected_properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);
        $private_properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);
        $properties = array_merge($protected_properties, $private_properties);
        foreach ($properties as $var) {
            $var = $var->getName();
            $reflection = new Reflection($class, $var);
            if (!$reflection->isObject())
                continue;
            if (is_iterable($var)) {
                $recursive_objs = [];
                foreach ($var as $obj) {
                    $ChildClass = $reflection->getClassName();
                    $recursive_objs[] = new $ChildClass($obj);
                }
            }
            $getValue = 'get' . $this->processSnakeToPascal($var);
            $objs[$var] = $this->$getValue();
        }

        return $objs ?? [];
    }

    public function hasPropertyCorrespondingToMethod(string $method_name): bool
    {
        if (substr($method_name, 0, 3) != 'get')
            return FALSE;
        $VarName = substr($method_name, 3);
        $varName = lcfirst($VarName);
        $var_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $varName));

        return property_exists(get_called_class(), $var_name) ? TRUE : FALSE;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set the value of active
     *
     * @return  self
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the value of deleted
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set the value of deleted
     *
     * @return  self
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    public static function getNamespace(): string
    {
        return get_called_class();
    }

    public function xd(): Generator
    {
        for ($i = 0; $i <= 10; $i++) {
            yield $i;
        }
    }

    private function processSnakeToPascal(string $text): string {
        $result = str_replace('_', '', ucwords($text, '_'));

        return $result;
    }
}
